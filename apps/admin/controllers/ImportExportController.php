<?php

use apps\admin\forms\blocks as BlocksForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class ImportExportController extends ControllerBase
{

    protected static $baseColumns = [
        'id_tipologia_stato',
        'titolo',
        'slug',
        'excerpt',
        'testo',
        'data_inizio_pubblicazione',
        'data_fine_pubblicazione'
    ];
    /**
     * @var DateTime
     */
    protected $now;

    public function initialize()
    {

        $this->tag->setTitle('Import Export');
        $this->now = new DateTime();
        parent::initialize();

    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->tipologie_post = TipologiePost::find();
    }

    public function importAction()
    {
        if ($this->request->isPost() && $this->request->hasFiles() && $this->request->hasPost('id_tipologia_post')) {
            set_time_limit(0);
            $postType = TipologiePost::findFirstById($this->request->getPost('id_tipologia_post'));

            if (!$postType) {
                $this->flashSession->error('Post Type non trovato');
                return $this->response->redirect($this->controllerName . '/index');
            }

            $file = $this->request->getUploadedFiles();
            $file = $file[0];

            $columns = [];
            $entities = [];

            $row = 0;
            $nr_column = 0;

            if (($handle = fopen($file->getTempName(), "r")) !== false) {
                while (($rs = fgetcsv($handle, null, ",")) !== false) {
                    if ($row == 0) {

                        $nr_column = count($rs);
                        for ($i = 0; $i < $nr_column; $i++) {
                            $columns[] = $rs[$i];
                        }

                        if ($columns !== self::getColumns($postType)) {
                            $this->flashSession->error('Formato CSV non valido per Tipologia post: ' . $postType->descrizione);
                            return $this->response->redirect($this->controllerName . '/index');
                        }

                    } else {
                        $entity = [];
                        for ($i = 0; $i < $nr_column; $i++) {
                            $entity[$columns[$i]] = $rs[$i];
                        }
                        $entities[] = $entity;
                    }

                    $row++;
                }
                fclose($handle);
            }
            if (empty($entities)) {
                $this->flashSession->error('CSV Vuoto');
                return $this->response->redirect($this->controllerName . '/index');
            }
            $import = $this->initImport($entities, $postType);

            if (count($import['rejected']) > 0) {
                if ($import['imported'] > 0) $this->flash->success('Importati correttamente ' . $import['imported'] . ' contenuti');
                $this->flash->warning('Importazione arrestata per ' . count($import['rejected']) . ' contenuti');
                $this->view->success = $import['imported'];
                $this->view->errors = $import['rejected'];
            } else {
                $this->flashSession->success('Importazione completata per ' . $import['imported'] . ' contenuti');
                return $this->response->redirect($this->controllerName . '/index');
            }


        } else {
            $this->flashSession->error('Richiesta non Valida');
            $this->response->redirect($this->controllerName . '/index');
        }
    }

    private function getColumns(TipologiePost $postType)
    {
        $deafault_app = \Phalcon\Di::getDefault()->get('config')->application->defaultCode;
        $optionsMeta = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_meta');
        $optionsFilters = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_filter');

        $columns = self::$baseColumns;

        if ($optionsMeta && $optionsMeta->option_value !== '[]') {
            $meta = json_decode($optionsMeta->option_value, true);
            array_walk($meta, function (&$item) {
                $item = 'meta_' . $item;
            });
            $columns = array_merge($columns, $meta);
        }

        if ($optionsFilters && $optionsFilters->option_value !== '[]') {
            $filters = json_decode($optionsFilters->option_value, true);
            $nr = count($filters);
            for ($i = 0; $i < $nr; $i++) {
                if (strpos($filters[$i], 'key_') !== false) {
                    unset($filters[$i]);
                }
            }
            array_walk($filters, function (&$item) {
                $item = 'filter_' . $item;
            });
            $columns = array_merge($columns, $filters);
        }

        return $columns;
    }

    private function initImport($entities, $postType)
    {
        $nr = count($entities);

        $imported = 0;
        $rejected = [];

        for ($i = 0; $i < $nr; $i++) {
            $entity = $entities[$i];
            /**
             * Check if posts with same slug and typology already exists
             */
            if (empty($entity['slug'])) $entity['slug'] = self::slugify($entity['titolo'], true);
            $postExisting = Posts::findFirst([
                'conditions' => 'id_tipologia_post = :id_tipologia_post: AND slug = :slug: AND attivo = 1',
                'bind'       => [
                    'id_tipologia_post' => $postType->id,
                    'slug'              => $entity['slug']
                ]
            ]);
            if ($postExisting) {
                $rejected[] = '<span class="label label-warning">Attenzione</span> Riga ' . ($i + 1) . ' slug "' . $entity['slug'] . '" già inserito a sistema, modificarlo nel csv se si desidera importarlo';
                continue;
            }
            $transaction = $this->beginTransaction();
            $images_list = [];

            try {
                $post = new Posts();
                $post->id_applicazione = \Phalcon\Di::getDefault()->get('config')->application->defaultId;
                $post->id_tipologia_stato = $entity['id_tipologia_stato'];
                $post->id_tipologia_post = $postType->id;
                $post->slug = parent::slugify($entity['slug'], true);
                $post->titolo = $entity['titolo'];
                $post->excerpt = substr(strip_tags($entity['excerpt']), 0, 245) . '...';
                $post->testo = $entity['testo'];
                $post->data_inizio_pubblicazione = !empty($entity['data_inizio_pubblicazione']) ? $entity['data_inizio_pubblicazione'] : $this->now->format('Y-m-d H:i:s');
                $post->data_fine_pubblicazione = !empty($entity['data_fine_pubblicazione']) ? $entity['data_fine_pubblicazione'] : null;
                $post->data_creazione = $this->now->format('Y-m-d H:i:s');
                $post->id_utente = $this->auth['id'];
                $post->attivo = 1;

                if (!$post->save()) {
                    $messages = [];
                    foreach ($post->getMessages() as $message) {
                        $messages[] = $message;
                    }
                    $transaction->rollback('<span class="label label-danger">Errore</span> Post save ' . implode(' - ', $messages));
                }

                foreach ($entity as $key => $val) {

                    if (in_array($key, self::$baseColumns)) continue;

                    if (($pos = strpos($key, 'meta_')) === 0) {

                        $meta_key = substr_replace($key, '', $pos, strlen('meta_'));

                        $meta = Meta::findFirst([
                            'conditions' => 'key = :meta_key: AND attivo = 1',
                            'bind'       => ['meta_key' => $meta_key]
                        ]);
                        if (!$meta) $transaction->rollback('<span class="label label-danger">Errore</span> Meta ' . $meta_key . ' non trovato');

                        //se è una select
                        if ($meta->id_tipologia_meta == 6 && !empty($meta->dataset)) {
                            $data_values = explode('|', $meta->dataset);
                            $avaialble_values = [];
                            foreach ($data_values as $dv) {
                                list($k, $v) = explode(':', $dv, 2);
                                $avaialble_values[] = $k;
                            }

                            if (empty($val) || !in_array($val, $avaialble_values)) {
                                $val = $meta->required ? $avaialble_values[0] : null;
                            }
                        }

                        //se è un file
                        if (($meta->id_tipologia_meta == 8) && filter_var($val, FILTER_VALIDATE_URL)) {
                            $filename = uniqid() . basename($val);
                            $image = self::downloadImg($val);
                            file_put_contents(FILES_DIR . $filename, $image);

                            if (!file_exists(FILES_DIR . $filename)) $transaction->rollback('<span class="label label-danger">Errire</span> Impossibile scrivere file ' . $val);
                            $images_list[] = FILES_DIR . $filename;

                            $file = new Files();
                            $file->id_tipologia_stato = 1;
                            $file->original_filename = $filename;
                            $file->filename = $filename;
                            $file->filetype = 'image/jpeg';
                            $file->filesize = filesize(FILES_DIR . $filename);
                            $file->filepath = '/public/files';
                            $file->fileurl = $this->config->application->siteUri . '/files/' . $filename;
                            $file->priorita = $i;
                            $file->alt = $post->titolo;
                            $file->data_creazione = $this->now->format('Y-m-d H:i:s');
                            $file->attivo = 1;
                            if (!$file->save()) {
                                $messages = [];
                                foreach ($file->getMessages() as $message) {
                                    $messages[] = $message;
                                }
                                $transaction->rollback('<span class="label label-danger">Errore</span> File save ' . implode(' - ', $messages));
                            }
                            \apps\admin\library\ImageHandler::getIstance()->regenerateThumbnails($file);
                            $val = $file->id;
                        }

                        // se sono più file
                        if (($meta->id_tipologia_meta == 9) && !empty($val)) {
                            $urls = explode(',', $val);
                            $new_val = [];
                            foreach ($urls as $image_url) {
                                if (!filter_var($image_url, FILTER_VALIDATE_URL)) continue;
                                $filename = uniqid() . basename($image_url);
                                $image = self::downloadImg($image_url);
                                file_put_contents(FILES_DIR . $filename, $image);

                                if (!file_exists(FILES_DIR . $filename)) $transaction->rollback('<span class="label label-danger">Errore</span> Impossibile scrivere file ' . $val);
                                $images_list[] = FILES_DIR . $filename;

                                $file = new Files();
                                $file->id_tipologia_stato = 1;
                                $file->original_filename = $filename;
                                $file->filename = $filename;
                                $file->filetype = 'image/jpeg';
                                $file->filesize = filesize(FILES_DIR . $filename);
                                $file->filepath = '/public/files';
                                $file->fileurl = $this->config->application->siteUri . '/files/' . $filename;
                                $file->priorita = $i;
                                $file->alt = $post->titolo;
                                $file->data_creazione = $this->now->format('Y-m-d H:i:s');
                                $file->attivo = 1;
                                if (!$file->save()) {
                                    $messages = [];
                                    foreach ($file->getMessages() as $messages) {
                                        $messages[] = $message;
                                    }
                                    $transaction->rollback('<span class="label label-danger">Errore</span> File save ' . implode(' - ', $messages));
                                }
                                \apps\admin\library\ImageHandler::getIstance()->regenerateThumbnails($file);
                                $new_val[] = $file->id;
                            }
                            $val = implode(',', $new_val);
                        }

                        if (!is_null($val)) {

                            if (empty($val)) $val = null;

                            $post_meta = new PostsMeta();
                            $post_meta->id_meta = $meta->id;
                            $post_meta->meta_key = $meta->key;
                            $post_meta->post_id = $post->id;
                            $post_meta->id_tipologia_post_meta = $meta->id_tipologia_meta;
                            $post_meta->id_tipologia_stato = 1;
                            $post_meta->attivo = 1;
                            $post_meta->data_creazione = $this->now->format('Y-m-d H:i:s');
                            $post_meta = $post_meta->setMetaValue($post_meta, $meta->TipologieMeta->descrizione, $val);
                            if (!$post_meta->save()) {
                                $messages = "";
                                foreach ($post_meta->getMessages() as $message) {
                                    $messages[] = $message;
                                }
                                $transaction->rollback('<span class="label label-danger">Errore</span> PostsMeta save ' . implode(' - ', $messages));
                            }
                        }

                    } elseif (($pos = strpos($key, 'filter_')) === 0 && !empty($val) && $val !== 'null') {

                        $filter_key = substr_replace($key, '', $pos, strlen('filter_'));

                        if (($pos = strpos($filter_key, 'key_')) === 0) {
                            $filter_key = substr_replace($filter_key, '', $pos, strlen('key_'));
                        }

                        $filter = Filtri::findFirst([
                            'conditions' => 'key = :key: AND id_tipologia_stato AND attivo = 1',
                            'bind'       => ['key' => $filter_key]
                        ]);
                        if (!$filter) $transaction->rollback('Filter ' . $filter_key . ' non trovato');

                        $filtro_valore = FiltriValori::findFirst([
                            'conditions' => '(valore = :valore: OR key = :valore:) AND id_filtro = :id_filtro: AND attivo = 1',
                            'bind'       => ['valore' => trim($val), 'id_filtro' => $filter->id]
                        ]);

                        if (!$filtro_valore) {
                            $filtro_valore = new FiltriValori();
                            $filtro_valore->id_filtro = $filter->id;
                            $filtro_valore->id_filtro_valore_parent = null;
                            $filtro_valore->valore = $val;
                            $filtro_valore->key = parent::slugify($val);
                            $filtro_valore->numeric_key = null;
                            $filtro_valore->data_creazione = $this->now->format('Y-m-d H:i:s');
                            $filtro_valore->data_aggiornamento = $this->now->format('Y-m-d H:i:s');
                            $filtro_valore->attivo = 1;
                            if (!$filtro_valore->save()) {
                                $messages = [];
                                foreach ($filtro_valore->getMessages() as $message) {
                                    $messages[] = $message;
                                }
                                $transaction->rollback('<span class="label label-danger">Errore</span> FiltriValori save ' . implode(' - ', $messages));
                            }
                        }
                        $id_filtro_valore = $filtro_valore->id;

                        $posts_filtri = new PostsFiltri();
                        $posts_filtri->id_post = $post->id;
                        $posts_filtri->id_filtro = $filter->id;
                        $posts_filtri->id_filtro_valore = $id_filtro_valore;
                        $posts_filtri->data_creazione = $this->now->format('Y-m-d H:i:s');
                        $posts_filtri->data_aggiornamento = $this->now->format('Y-m-d H:i:s');
                        $posts_filtri->attivo = 1;
                        if (!$posts_filtri->save()) {
                            $messages = [];
                            foreach ($posts_filtri->getMessages() as $message) {
                                $messages[] = $message;
                            }
                            $transaction->rollback('<span class="label label-danger">Errore</span> PostsFilti save ' . implode(' - ', $messages));
                        }

                    } else {
                        continue;
                    }
                }
            } catch (Exception $e) {

                //PhalconDebug::debug($e);
                $rejected[] = '<span class="label label-danger">Errore</span>  : Riga ' . ($i + 1) . ' - ' . $e->getMessage();
                if (!empty($images_list)) {
                    foreach ($images_list as $tmp_img) {
                        unlink($tmp_img);
                    }
                }
                continue;
            }
            $imported = $imported + 1;
            $transaction->commit();
            $post->triggerSave();
        }

        return [
            'imported' => $imported,
            'rejected' => $rejected
        ];

    }

    private static function downloadImg($url)
    {
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg, image/png,';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'php';
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERAGENT, $user_agent); //check here
        curl_setopt($process, CURLOPT_TIMEOUT, 60);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }

    public function exportAction()
    {
        if ($this->request->isPost() && $this->request->hasPost('id_tipologia_post')) {

            $postType = TipologiePost::findFirstById($this->request->getPost('id_tipologia_post'));

            if (!$postType) {
                $this->flashSession->error('Post Type non trovato');
                return $this->response->redirect($this->controllerName . '/index');
            }
            $deafault_app = $this->config->application->defaultCode;
            if ($postType) {
                try {
                    $this->view->disable();
                    $optionsMeta = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_meta');
                    $optionsFilters = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_filter');
                    ob_end_clean();
                    $columns = [
                        'id_tipologia_stato',
                        'titolo',
                        'slug',
                        'excerpt',
                        'testo',
                        'data_inizio_pubblicazione',
                        'data_fine_pubblicazione'
                    ];
                    $rows = [];
                    if ($optionsMeta && $optionsMeta->option_value !== '[]') {
                        $meta = json_decode($optionsMeta->option_value, true);
                        array_walk($meta, function (&$item) {
                            $item = 'meta_' . $item;
                        });
                        $columns = array_merge($columns, $meta);
                    }

                    if ($optionsFilters && $optionsFilters->option_value !== '[]') {
                        $filters = json_decode($optionsFilters->option_value, true);
                        $nr = count($filters);
                        for ($i = 0; $i < $nr; $i++) {
                            if (strpos($filters[$i], 'key_') !== false) {
                                unset($filters[$i]);
                            }
                        }
                        array_walk($filters, function (&$item) {
                            $item = 'filter_' . $item;
                        });
                        $columns = array_merge($columns, $filters);
                    }

                    $posts = Posts::find([
                        'conditions' => 'id_tipologia_post = :post_type_id:',
                        'bind'       => ['post_type_id' => $postType->id]
                    ]);

                    foreach ($posts as $post) {
                        $data_inizio_pubblicazione = is_null($post->data_inizio_pubblicazione) ? '' : $post->data_inizio_pubblicazione;
                        $data_fine_pubblicazione = is_null($post->data_fine_pubblicazione) ? '' : $post->data_fine_pubblicazione;
                        $rows = [
                            $post->id_tipologia_stato,
                            $post->titolo,
                            $post->slug,
                            $post->excerpt,
                            $post->testo,
                            $data_inizio_pubblicazione,
                            $data_fine_pubblicazione
                        ];
                        foreach ($meta as $m) {
                            $m = substr_replace($m, '', 0, strlen('meta_'));
                            $pm = PostsMeta::findFirst([
                                'conditions' => 'meta_key = :mkey: AND post_id = :id_post: AND id_tipologia_stato = 1',
                                'bind'       => ['mkey' => $m, 'id_post' => $post->id]
                            ]);
                            $metaval = PostsMeta::getMetaValue($pm);
                            if ($pm->id_tipologia_post_meta == 8 && !empty($metaval) && !is_null($metaval)) {
                                $file = Files::findFirstById($metaval);
                                if ($file) $metaval = $file->fileurl;
                            }
                            if ($pm->id_tipologia_post_meta == 9 && !empty($metaval) && !is_null($metaval)) {
                                $files = Files::find([
                                    'columns'    => 'GROUP_CONCAT(Files.fileurl) AS file_list',
                                    'conditions' => 'id IN(' . $metaval . ') AND attivo = 1',
                                    'order'      => 'priorita'
                                ]);
                                if ($files) $metaval = $files->file_list;
                            }
                            if (is_null($metaval)) $metaval = '';
                            $rows[] = $metaval;
                        }

                        foreach ($filters as $f) {
                            $f = str_replace('filter_', '', $f);
                            $fm = FiltriValori::findFirst([
                                'conditions' => 'f.key = :key_filtro: AND pf.id_post = :id_post: AND FiltriValori.attivo = 1',
                                'joins'      => [
                                    ['Filtri', 'f.id = FiltriValori.id_filtro AND f.attivo = 1', 'f', 'INNER'],
                                    ['PostsFiltri', 'pf.id_filtro_valore = FiltriValori.id AND pf.id_filtro = f.id AND pf.attivo = 1', 'pf', 'INNER'],
                                ],
                                'bind'       => [
                                    'key_filtro' => $f,
                                    'id_post'    => $post->id
                                ],
                                'group'      => 'FiltriValori.id',
                            ]);
                            if (!$fm) {
                                $filter_val = '';
                            } else {
                                $filter_val = $fm->valore;
                            }
                            $rows[] = $filter_val;
                        }
                    }

                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment;filename="' . $postType->slug . '_csv_export_' . $this->now->format('YmdHis') . '.csv";');

                    // open the "output" stream
                    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
                    ob_clean();
                    $f = fopen('php://output', 'w');

                    fputcsv($f, $columns);
                    fputcsv($f, $rows);
                    exit();
                } catch (Exception $e) {
                    $this->flashSession->error($e->getMessage());
                    $this->response->redirect($this->controllerName . '/index/');
                }
            } else {
                $this->flashSession->error('Impossibile esportare CSV');
                return $this->response->redirect($this->controllerName . '/index/');
            }
        } else {
            $this->flashSession->error('Richiesta non valida');
            return $this->response->redirect($this->controllerName . '/index/');
        }
    }

    public function modelAction()
    {
        if ($this->request->isPost() && $this->request->hasPost('id_tipologia_post')) {
            $deafault_app = $this->config->application->defaultCode;
            $postType = TipologiePost::findFirstById($this->request->getPost('id_tipologia_post'));
            if ($postType) {
                try {
                    $this->view->disable();
                    $optionsMeta = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_meta');
                    $optionsFilters = \Options::findFirstByOptionName('columns_map_' . $deafault_app . '_' . $postType->slug . '_filter');
                    ob_end_clean();
                    $columns = [
                        'id_tipologia_stato',
                        'titolo',
                        'slug',
                        'excerpt',
                        'testo',
                        'data_inizio_pubblicazione',
                        'data_fine_pubblicazione'
                    ];
                    if ($optionsMeta && $optionsMeta->option_value !== '[]') {
                        $meta = json_decode($optionsMeta->option_value, true);
                        array_walk($meta, function (&$item) {
                            $item = 'meta_' . $item;
                        });
                        $columns = array_merge($columns, $meta);
                    }

                    if ($optionsFilters && $optionsFilters->option_value !== '[]') {
                        $filters = json_decode($optionsFilters->option_value, true);
                        $nr = count($filters);
                        for ($i = 0; $i < $nr; $i++) {
                            if (strpos($filters[$i], 'key_') !== false) {
                                unset($filters[$i]);
                            }
                        }
                        array_walk($filters, function (&$item) {
                            $item = 'filter_' . $item;
                        });
                        $columns = array_merge($columns, $filters);
                    }
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment;filename="' . $postType->slug . '_csv_model.csv";');

                    // open the "output" stream
                    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
                    ob_clean();
                    $f = fopen('php://output', 'w');

                    fputcsv($f, $columns);
                    exit();
                } catch (Exception $e) {
                    $this->flashSession->error($e->getMessage());
                    return $this->response->redirect($this->controllerName . '/index');
                }
            } else {
                $this->flashSession->error('Impossibile esportare CSV per questo post type');
                return $this->response->redirect($this->controllerName . '/index');
            }

        } else {
            $this->flashSession->error('Richiesta non valida');
            return $this->response->redirect($this->controllerName . '/index/');
        }
    }


}
