<?php

class builder
{
    private $config;
    private $controller_data = array(
        'CONTROLLER_NAME' => '',
        'CONTROLLER_NAME_SINGOLARE' => '',
        'TITOLO_SINGOLARE' => '',
        'TITOLO_PLURALE' => '',
        'JOIN_RICERCA_TABELLA' => '',
        'JOIN_RICERCA_ALIAS' => '',
        'JOIN_RICERCA_CAMPO' => '',
        'RENDER_PAGING_OBJ' => ''
    );
    private $menu = array(
        'descrizione_menu_padre' => '',
        'descrizione_menu' => '',
        'icona_menu' => ''
    );
    private $flags = array(
        'history' => false,
        'email_segnalazioni' => false
    );
    private $options = array(
        'permessi' => false,
        'menu' => false,
        'override' => false
    );
    private $arr_replace_files;
    private $_path = '../';
    private $permessi = 0775;
    private $curl_model_url = 'http://www.cms.io/webtools.php?_url=/models/create';
    private $curl_model_data = array(
        'defineRelations' => 1,
        'foreignKeys' => 1,
        'directory' => BASE_DIR,
        'namespace' => '',
        'schema' => 'cmsio',
        'tableName' => ''
    );

    public function __construct(array $controller_data, array $flags, array $options, array $menu)
    {
        $this->controller_data = array_merge($this->controller_data, $controller_data);
        $this->flags = array_merge($this->flags, $flags);
        $this->options = array_merge($this->options, $options);
        $this->menu = array_merge($this->menu, $menu);

        $this->controller_data ['TITOLO_SINGOLARE_LOWERCASE'] = strtolower($this->controller_data ['TITOLO_SINGOLARE']);
        $this->controller_data ['ROUTE'] = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $this->controller_data ['CONTROLLER_NAME'])), '_');
        $this->controller_data ['NOME_SINGOLARE'] = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $this->controller_data ['CONTROLLER_NAME_SINGOLARE'])), '_');

        $this->config = include APP_DIR . "/config/config.php";
    }

    public function run()
    {
        $this->setup_db();

        // inizializza le chiavi dell'array con il contenuto dei tpl
        $tpl = array(
            'standard' => array(
                'controller' => file_get_contents('builder/tpl/app/controllers/Controller.txt'),
                'form_index' => file_get_contents('builder/tpl/app/forms/IndexForm.txt'),
                'form_edit' => file_get_contents('builder/tpl/app/forms/EditForm.txt'),
                'form_new' => file_get_contents('builder/tpl/app/forms/NewForm.txt'),
                'view_index' => file_get_contents('builder/tpl/app/views/index.txt'),
                'view_edit' => file_get_contents('builder/tpl/app/views/edit.txt'),
                'view_new' => file_get_contents('builder/tpl/app/views/new.txt'),
                'js_edit' => file_get_contents('builder/tpl/public/js/edit.txt')
            ),
            'history' => array(
                'controller_history' => file_get_contents('builder/tpl/app/controllers/ControllerHistory.txt'),
                'view_partial_history' => file_get_contents('builder/tpl/app/views/partials/historyModal.txt')
            ),
            'email_segnalazioni' => array(
                'controller_email_segnalazioni' => file_get_contents('builder/tpl/app/controllers/ControllerEmailSegnalazioni.txt'),
                'view_email_template_segnalazione' => file_get_contents('builder/tpl/app/views/emailTemplates/segnalazione.txt'),
                'view_partial_email_segnalazioni_detail_modal' => file_get_contents('builder/tpl/app/views/partials/emailSegnalazioniDetailModal.txt'),
                'view_partial_email_segnalazioni_list' => file_get_contents('builder/tpl/app/views/partials/emailSegnalazioniList.txt'),
                'view_partial_email_segnalazioni_modal' => file_get_contents('builder/tpl/app/views/partials/emailSegnalazioniModal.txt')
            )
        );

        // inizializza le chiavi dell'array con il contenuto dei tpl da sostituire nei file
        $tpl_replace_files = array(
            'history' => array(
                'view_edit_history' => array(
                    'find' => 'VIEW_HISTORY',
                    'tpl' => 'view_edit',
                    'content' => file_get_contents('builder/tpl_replace_files/app/views/editHistory.txt')
                ),
                'js_edit_history' => array(
                    'find' => 'JS_HISTORY',
                    'tpl' => 'js_edit',
                    'content' => file_get_contents('builder/tpl_replace_files/public/js/editHistory.txt')
                )
            ),
            'email_segnalazioni' => array(
                'view_edit_email_segnalazioni' => array(
                    'find' => 'VIEW_EMAIL_SEGNALAZIONI',
                    'tpl' => 'view_edit',
                    'content' => file_get_contents('builder/tpl_replace_files/app/views/editEmailSegnalazioni.txt')
                ),
                'js_edit_email_segnalazioni' => array(
                    'find' => 'JS_EMAIL_SEGNALAZIONI',
                    'tpl' => 'js_edit',
                    'content' => file_get_contents('builder/tpl_replace_files/public/js/editEmailSegnalazioni.txt')
                )
            )
        );

        // sostituisce $tpl_replace_files in $tpl
        foreach ($tpl_replace_files as $type => $arr) {
            if (!$this->flags [$type])
                continue;
            foreach ($arr as $key => $val) {
                $tpl ['standard'] [$val ['tpl']] = $data = str_replace("<!-- " . $val ['find'] . " !-->", $val ['content'], $tpl ['standard'] [$val ['tpl']]);
            }
        }

        $files = array(
            'standard' => array(
                'controller' => array(
                    'path' => 'apps/admin/controllers/',
                    'filename' => $this->controller_data ['CONTROLLER_NAME'] . 'Controller.php'
                ),
                'form_index' => array(
                    'path' => 'apps/admin/forms/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'IndexForm.php'
                ),
                'form_edit' => array(
                    'path' => 'apps/admin/forms/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'EditForm.php'
                ),
                'form_new' => array(
                    'path' => 'apps/admin/forms/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'NewForm.php'
                ),
                'view_index' => array(
                    'path' => 'apps/admin/views/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'index.phtml'
                ),
                'view_edit' => array(
                    'path' => 'apps/admin/views/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'edit.phtml'
                ),
                'view_new' => array(
                    'path' => 'apps/admin/views/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'new.phtml'
                ),
                'js_edit' => array(
                    'path' => 'public/assets/admin/js/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'edit.js'
                )
            ),
            'history' => array(
                'controller_history' => array(
                    'path' => 'apps/admin/controllers/',
                    'filename' => $this->controller_data ['CONTROLLER_NAME'] . 'HistoryController.php'
                ),
                'view_partial_history' => array(
                    'path' => 'apps/admin/views/partials/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => $this->controller_data ['CONTROLLER_NAME'] . 'HistoryModal.phtml'
                )
            ),
            'email_segnalazioni' => array(
                'controller_email_segnalazioni' => array(
                    'path' => 'apps/admin/controllers/',
                    'filename' => $this->controller_data ['CONTROLLER_NAME'] . 'EmailSegnalazioniController.php'
                ),
                'view_email_template_segnalazione' => array(
                    'path' => 'apps/admin/views/emailTemplates/',
                    'filename' => $this->controller_data ['CONTROLLER_NAME'] . 'Segnalazione.phtml'
                ),
                'view_partial_email_segnalazioni_detail_modal' => array(
                    'path' => 'apps/admin/views/partials/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'emailSegnalazioniDetailModal.phtml'
                ),
                'view_partial_email_segnalazioni_list' => array(
                    'path' => 'apps/admin/views/partials/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'emailSegnalazioniList.phtml'
                ),
                'view_partial_email_segnalazioni_modal' => array(
                    'path' => 'apps/admin/views/partials/' . $this->controller_data ['ROUTE'] . '/',
                    'filename' => 'emailSegnalazioniModal.phtml'
                )
            )
        );
        $this->setup_model();

        $this->create_files($files ['standard'], $tpl ['standard']);

        foreach ($this->flags as $type => $active) {
            if (!$active) continue;
            $this->create_files($files [$type], $tpl [$type]);
        }

    }

    private function setup_db()
    {
        $db = new db ($this->config->database->host, $this->config->database->username, $this->config->database->password, $this->config->database->dbname);

        $table = $this->controller_data ['ROUTE'];
        $table_history = $table . '_history';
        $table_email_segnalazioni = $table . '_email_segnalazioni';

        // verifica la presenza delle tabelle nel DB
        $query = "SELECT
					t.TABLE_NAME as tabella
				  FROM
					information_schema.tables t 
				  WHERE
					t.table_schema = '{$this->config->database->dbname}' 
				  AND
					t.table_name IN ('{$table}','{$table_history}','{$table_email_segnalazioni}')
				 ";
        $rs = $db->db_query($query);

        if (!empty ($rs)) {

            $arr_table = array();

            $count = count($rs);
            for ($i = 0; $i < $count; $i++) {
                $arr_table [] = $rs [$i] ['tabella'];
            }

            if (!in_array($table, $arr_table))
                exit ();

            $rs_fields_table = $db->db_table_describe($table);

            $count = count($rs_fields_table);
            for ($i = 0; $i < $count; $i++) {
                if (strpos($rs_fields_table [$i] ['Field'], 'id_tipologia_') !== false) {
                    if ($rs_fields_table [$i] ['Field'] == 'id_tipologia_stato') {
                        $tableName = 'tipologie_stato_' . $this->controller_data ['NOME_SINGOLARE'];
                    } else {
                        $tableName = str_replace('id_tipologia_', 'tipologie_', $rs_fields_table [$i] ['Field']);
                    }
                    $this->setup_model_dependency($tableName);
                }
            }

            // verifica se � richiesta la history e se esiste la tabella. Se necessario la crea
            if ($this->flags ['history'] && !in_array($table_history, $arr_table)) {

                $id_type_length = substr($rs_fields_table [0] ['Type'], stripos($rs_fields_table [0] ['Type'], '(') + 1, 1);
                $id_type = str_replace($id_type_length, $id_type_length + 1, $rs_fields_table [0] ['Type']);

                $fields = array();
                $count = count($rs_fields_table);
                for ($i = 0; $i < $count; $i++) {

                    if ($rs_fields_table [$i] ['Field'] == 'id') {
                        $rs_fields_table [$i] ['Field'] = 'id_history';
                        $rs_fields_table [$i] ['Extra'] = '';
                    }
                    if ($rs_fields_table [$i] ['Field'] == 'attivo')
                        $rs_fields_table [$i] ['Default'] = '';

                    $arr = array(
                        $rs_fields_table [$i] ['Field'],
                        $rs_fields_table [$i] ['Type'],
                        ($rs_fields_table [$i] ['Null'] == 'NO') ? 'NOT NULL' : 'NULL',
                        ($rs_fields_table [$i] ['Default'] != '' && stripos($rs_fields_table [$i] ['Default'], 'CURRENT') !== false) ? 'DEFAULT ' . $rs_fields_table [$i] ['Default'] : '',
                        $rs_fields_table [$i] ['Extra']
                    );
                    $fields [] = implode(' ', $arr);
                }

                array_unshift($fields, "id {$id_type} NOT NULL AUTO_INCREMENT");
                array_push($fields, "PRIMARY KEY (id)");

                $query = "CREATE TABLE {$table_history} (" . implode(' , ', $fields) . ") COLLATE='utf8_swedish_ci' ENGINE=ARCHIVE ROW_FORMAT=COMPACT AUTO_INCREMENT=0";
                $db->db_query($query);
            }

            // verifica se � richiesta email_segnalazioni e se esiste la tabella. Se necessario la crea
            if ($this->flags ['email_segnalazioni'] && !in_array($table_email_segnalazioni, $arr_table)) {

                $query = "CREATE TABLE {$table_email_segnalazioni} (
							id MEDIUMINT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
							id_{$this->controller_data['NOME_SINGOLARE']} {$rs_fields_table[0]['Type']} NOT NULL,
							destinatari TEXT NOT NULL COLLATE 'utf8_swedish_ci',
							messaggio TEXT NOT NULL COLLATE 'utf8_swedish_ci',
							data_creazione DATETIME NOT NULL,
							data_aggiornamento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
							id_utente TINYINT(4) UNSIGNED NOT NULL,
							attivo TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							PRIMARY KEY (id),
							INDEX attivo (attivo),
							INDEX fk_{$this->controller_data['ROUTE']}_email_segnalazioni_caricamenti_ordini (id_{$this->controller_data['NOME_SINGOLARE']}),
							INDEX fk_{$this->controller_data['ROUTE']}_email_segnalazioni_utenti (id_utente),
							CONSTRAINT fk_{$this->controller_data['ROUTE']}_email_segnalazioni_{$this->controller_data['ROUTE']} FOREIGN KEY (id_{$this->controller_data['NOME_SINGOLARE']}) REFERENCES {$this->controller_data['ROUTE']} (id) ON UPDATE CASCADE ON DELETE NO ACTION,
							CONSTRAINT fk_{$this->controller_data['ROUTE']}_segnalazioni_utenti FOREIGN KEY (id_utente) REFERENCES utenti (id) ON UPDATE CASCADE ON DELETE NO ACTION
						  ) COLLATE='utf8_swedish_ci' ENGINE=InnoDB ROW_FORMAT=COMPACT AUTO_INCREMENT=0
						";
                $db->db_query($query);
            }

            if ($this->options ['permessi']) {

                $arr_ruoli_permessi [] = array(
                    'risorsa' => $this->controller_data ['ROUTE'],
                    'azione' => '[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]'
                );

                if ($this->flags ['history'])
                    $arr_ruoli_permessi [] = array(
                        'risorsa' => $this->controller_data ['ROUTE'] . '_history',
                        'azione' => '[\"search\"]'
                    );
                if ($this->flags ['email_segnalazioni'])
                    $arr_ruoli_permessi [] = array(
                        'risorsa' => $this->controller_data ['ROUTE'] . '_email_segnalazioni',
                        'azione' => '[\"create\",\"search\"]'
                    );

                $count = count($arr_ruoli_permessi);
                for ($i = 0; $i < $count; $i++) {

                    $query = "SELECT
								rp.id
							  FROM
								ruoli_permessi rp
							  WHERE
							  	rp.id_ruolo = 1
							  AND
							  	rp.livello = 0
							  AND
								rp.risorsa = '{$arr_ruoli_permessi[$i]['risorsa']}'
							  AND
							  	rp.attivo = 1
							  LIMIT 0,1
							 ";
                    $rs = $db->db_max($query);

                    if (!empty ($rs))
                        continue;

                    $query = "INSERT INTO
								ruoli_permessi
							  SET
								id_ruolo = 1,
								livello = 0,
								risorsa = '{$arr_ruoli_permessi[$i]['risorsa']}',
								azione = '{$arr_ruoli_permessi[$i]['azione']}',
								data_creazione = NOW(),
								attivo = 1
							 ";
                    $db->db_query($query);
                }
            }

            if ($this->options ['menu'] && !empty ($this->menu ['descrizione_menu_padre']) && !empty ($this->menu ['descrizione_menu'])) {

                $query = "SELECT
							GROUP_CONCAT(rm.descrizione) as descrizione
						  FROM
							ruoli_menu rm
						  WHERE
							rm.id_ruolo = 1
						  AND
							rm.livello = 0
						  AND
							rm.attivo = 1
						 ";
                $rs = $db->db_max($query);

                if (!empty ($rs)) {

                    $arr_menu = explode(',', $rs);

                    if (in_array($this->menu ['descrizione_menu'], $arr_menu))
                        return false;
                    if (!in_array($this->menu ['descrizione_menu_padre'], $arr_menu))
                        return false;

                    $query = "SELECT
								rm2.id_padre,
								MAX(rm2.ordine)+1 as ordine
							  FROM
								ruoli_menu rm2
							  WHERE
								rm2.id_padre = (
									SELECT
										IF(COUNT(rm.id) = 0, 0, rm.id) as id
									FROM
									ruoli_menu rm
									WHERE
										rm.id_ruolo = 1
									AND
										rm.livello = 0
									AND
										rm.risorsa = ''
									AND
										rm.azione = ''
									AND
										rm.descrizione = 'Siti web'
									AND
										rm.attivo = 1
								)
							 LIMIT 0,1
							";
                    $rs_menu = $db->db_query_max($query);

                    $query = "INSERT INTO
								ruoli_menu
							  SET
								id_ruolo = 1,
								livello = 0,
								risorsa = '',
								azione = '',
								descrizione = '{$this->menu['descrizione_menu']}',
								class = '{$this->menu['icona_menu']}',
								header = 0,
								visible = 1,
								id_padre = '{$rs_menu['id_padre']}',
								ordine = '{$rs_menu['ordine']}',
								data_creazione = NOW(),
								attivo = 1
							 ";
                    $db->db_query($query);

                    $id_ruolo_menu = $db->db_last_insert_id();

                    $query = "INSERT INTO
								ruoli_menu
							  SET
								id_ruolo = 1,
								livello = 0,
								risorsa = '{$this->controller_data['ROUTE']}',
								azione = 'index',
								descrizione = 'Cerca " . strtolower($this->menu ['descrizione_menu']) . "',
								class = 'fa-search',
								header = 0,
								visible = 1,
								id_padre = '{$id_ruolo_menu}',
								ordine = 1,
								data_creazione = NOW(),
								attivo = 1
							 ";
                    $db->db_query($query);
                }
            }
        } else {
            exit ();
        }
    }

    private function setup_model_dependency($fieldName)
    {
        $model = $this->create_model($fieldName);

        if ($model) {

            $data = file_get_contents($model);

            $find = '$key = \'' . $fieldName . '_find.\'.md5(json_encode($parameters));' . PHP_EOL . "\t";
            $find .= "\t" . '$rs = apcu_fetch($key);' . PHP_EOL . "\t";
            $find .= "\t" . 'if(!$rs){' . PHP_EOL . "\t";
            $find .= "\t\t" . '$rs = parent::find($parameters);' . PHP_EOL . "\t";
            $find .= "\t\t" . 'apcu_store($key, $rs);' . PHP_EOL . "\t\t" . '}' . PHP_EOL . "\t";
            $find .= "\t" . 'return $rs;';

            $findFist = '$key = \'' . $fieldName . '_find_first.\'.md5(json_encode($parameters));' . PHP_EOL . "\t";
            $findFist .= "\t" . '$rs = apcu_fetch($key);' . PHP_EOL . "\t";
            $findFist .= "\t" . 'if(!$rs){' . PHP_EOL . "\t";
            $findFist .= "\t\t" . '$rs = parent::findFirst($parameters);' . PHP_EOL . "\t";
            $findFist .= "\t\t" . 'apcu_store($key, $rs);' . PHP_EOL . "\t\t" . '}' . PHP_EOL . "\t";
            $findFist .= "\t" . 'return $rs;';

            $data = str_replace(array(
                'return parent::find($parameters);',
                'return parent::findFirst($parameters);'
            ), array(
                $find,
                $findFist
            ), $data);

            file_put_contents($model, $data);
        }
    }

    private function create_model($tableName)
    {
        $dir = $this->_path . 'apps/admin/models/';
        $filename = ucfirst(preg_replace_callback('/_([a-z])/', function ($match) {
                return strtoupper($match [1]);
            }, $tableName)) . '.php';

        if (file_exists($dir . $filename)) {
            if ($this->options ['override']) {
                unlink($dir . $filename);
            } else {
                return false;
            }
        }

        $this->curl_model_data ['tableName'] = $tableName;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->curl_model_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curl_model_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $dir . $filename;
    }

    private function setup_model()
    {
        $initialize_controller = '';

        $model = $this->create_model($this->controller_data ['ROUTE']);

        if ($model && ($this->flags ['history'] || $this->flags ['email_segnalazioni'])) {

            $data = file_get_contents($model);

            // se c'� history, copia il metodo inizialize del controller
            if ($this->flags ['history']) {
                $find = 'public function initialize()';
                $arr = explode($find, $data);

                $arr [1] = substr($arr [1], 0, stripos($arr [1], '}'));

                $initialize_controller = "\t" . $find . $arr [1] . "\t" . '$this->belongsTo(\'id_history\', \'' . $this->controller_data ['CONTROLLER_NAME'] . '\', \'id\', array(\'alias\' => \'' . $this->controller_data ['CONTROLLER_NAME'] . '\'));' . PHP_EOL . "\t" . '}';

                $initialize_controller = str_replace('$this->hasMany(\'id\', \'' . $this->controller_data ['CONTROLLER_NAME'] . 'EmailSegnalazioni\', \'id_' . $this->controller_data ['NOME_SINGOLARE'] . '\', array(\'alias\' => \'WebsiteEmailEmailSegnalazioni\'));', '', $initialize_controller);
            }

            $find = 'parent::initialize();';
            $arr = explode($find, $data);
            $arr [0] .= $find . PHP_EOL . PHP_EOL;

            if ($this->flags ['history']) {
                $arr [1] = "\t\t" . '$this->hasMany(\'id\', \'' . $this->controller_data ['CONTROLLER_NAME'] . 'History\', \'id_history\', array(\'alias\' => \'' . $this->controller_data ['CONTROLLER_NAME'] . 'History\', \'reusable\' => true));' . $arr [1];
            }
            /*
             * if($this->flags['email_segnalazioni']){
             * $arr[1] = '$this->hasMany(\'id\', \''.$this->controller_data['CONTROLLER_NAME'].'EmailSegnalazioni\', \'id_'.$this->controller_data['CONTROLLER_NAME'].'\', array(\'alias\' => \''.$this->controller_data['CONTROLLER_NAME'].'EmailSegnalazioni\', \'reusable\' => true, \'foreignKey\' => array(\'allowNulls\' => false, \'message\' => \'Violazione ForeignKey per [id]\', \'action\' => \Phalcon\Mvc\Model\Relation::ACTION_CASCADE)));'.$arr[1];
             * }
             */

            $data = implode('', $arr);

            file_put_contents($model, $data);
        }

        if ($this->flags ['history']) {
            $model = $this->create_model($this->controller_data ['ROUTE'] . '_history');

            if ($model) {

                $data = file_get_contents($model);
                $data = substr($data, 0, strrpos($data, '}'));

                $data .= $initialize_controller . PHP_EOL . '}';

                file_put_contents($model, $data);
            }
        }

        if ($this->flags ['email_segnalazioni']) {
            $model = $this->create_model($this->controller_data ['ROUTE'] . '_email_segnalazioni');

            if ($model) {

                $data = file_get_contents($model);
                $data = substr($data, 0, strrpos($data, '}'));

                $data .= "\t" . 'public function afterFetch(){' . PHP_EOL . "\t\t" . '$this->destinatari = json_decode($this->destinatari,true);' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
                $data .= "\t" . 'public function beforeSave(){' . PHP_EOL . "\t\t" . '$this->destinatari = json_encode($this->destinatari);;' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL . '}';

                file_put_contents($model, $data);
            }
        }
    }

    private function create_files($files, $tpl)
    {
        foreach ($files as $key => $val) {

            $dir = $this->_path . $val ['path'];
            $filename = $val ['filename'];
            $data = $tpl [$key];

            if (!is_dir($dir)) {
                if (mkdir($dir, $this->permessi, true)) {
                    chmod($dir, $this->permessi);
                } else {
                    continue;
                }
            }

            if (file_exists($dir . $filename)) {
                if ($this->options ['override']) {
                    unlink($dir . $filename);
                } else {
                    continue;
                }
            }

            if ($key == 'controller') {
                $join = empty ($this->controller_data ['JOIN_RICERCA_TABELLA']) ? '$query = $this->searchStandardOrdine($query, \'<!-- CONTROLLER_NAME !-->\', $search);' : '$query->innerJoin(\'<!-- JOIN_RICERCA_TABELLA !-->\', \'<!-- JOIN_RICERCA_ALIAS !-->.id = <!-- CONTROLLER_NAME !-->.<!-- JOIN_RICERCA_CAMPO !--> AND <!-- JOIN_RICERCA_ALIAS !-->.attivo = 1\', \'<!-- JOIN_RICERCA_ALIAS !-->\'); $query = $this->searchStandardOrdine($query, \'<!-- JOIN_RICERCA_ALIAS !-->\', $search);';
                $data = str_replace("<!-- SEARCH_STANDARD_ORDINE !-->", $join, $data);

                if ($this->flags ['history'])
                    $data = str_replace("<!-- CONTROLLER_DATA_HISTORY !-->", '$this->view->controller_data_history = $controller_data->get<!-- CONTROLLER_NAME !-->History(array(\'order\'=>\'id DESC\'));', $data);
                if ($this->flags ['email_segnalazioni'])
                    $data = str_replace("<!-- CONTROLLER_DATA_EMAIL_SEGNALAZIONI !-->", '$this->view->controller_data_email_segnalazioni = $controller_data->get<!-- CONTROLLER_NAME !-->EmailSegnalazioni(array(\'attivo = 1\',\'order\'=>\'id DESC\'));', $data);
            }

            foreach ($this->controller_data as $key => $val) {
                $data = str_replace("<!-- " . $key . " !-->", $val, $data);
            }

            file_put_contents($dir . $filename, preg_replace('/<!--.+?!-->/i', '', $data));
        }
    }
}

?>