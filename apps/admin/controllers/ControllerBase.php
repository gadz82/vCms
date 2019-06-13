<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Db\Column;
use Phalcon\Acl;
use Phalcon\Mvc\Model;

class ControllerBase extends Controller
{
    protected $controllerName;
    protected $alert_messagge;
    protected $jqGrid_columns = [];
    protected $grid_actions = [
        'delete',
        'edit',
        'clone',
        'edit_inline',
        'detail'
    ];
    protected $grid_inline_editable = [];
    protected $auth = [];
    protected $acl;

    public static function fromInput($dependencyInjector, $modelName, $data)
    {
        $criteria = new Criteria ();

        if (!empty ($data)) {

            $metaData = $dependencyInjector->getShared('modelsMetadata');
            $model = new $modelName ();
            $dataTypes = $metaData->getDataTypes($model);
            $bind = [];
            $where = 'where';
            $inWhere_bind_count = 0;

            foreach ($data as $fieldName => $value) {

                if (!isset ($dataTypes [$fieldName]))
                    continue;
                if (is_null($value) || empty ($value) || $value == '')
                    continue;

                if (is_array($value)) {

                    $criteria->inWhere($modelName . '.' . $fieldName, $value);
                    $count = count($value);
                    for ($i = 0; $i < $count; $i++) {
                        $bind ['ACP' . $inWhere_bind_count++] = $value [$i];
                    }
                } else if ($dataTypes [$fieldName] == Column::TYPE_CHAR || $dataTypes [$fieldName] == Column::TYPE_VARCHAR) {

                    $criteria->{$where} ($modelName . '.' . $fieldName . " LIKE :" . $fieldName . ":");
                    $bind [$fieldName] = '%' . $value . '%';
                } else if ($dataTypes [$fieldName] == Column::TYPE_DATE || $dataTypes [$fieldName] == Column::TYPE_DATETIME || $dataTypes [$fieldName] == Column::TYPE_TIMESTAMP) {

                    if (stripos($value, '[') !== false) {
                        preg_match_all('/([0-9-]{10})/', $value, $matches);
                        $criteria->betweenWhere('DATE_FORMAT(' . $modelName . '.' . $fieldName . ',"%Y-%m-%d")', $matches [0] [0], $matches [0] [1]);
                        $bind ['ACP' . $inWhere_bind_count++] = $matches [0] [0];
                        $bind ['ACP' . $inWhere_bind_count++] = $matches [0] [1];
                    } else {
                        $criteria->{$where} ($modelName . '.' . $fieldName . " = :" . $fieldName . ":");
                        $bind [$fieldName] = $value;
                    }
                } else {

                    $criteria->{$where} ($modelName . '.' . $fieldName . " = :" . $fieldName . ":");
                    $bind [$fieldName] = $value;
                }

                $where = 'andWhere';
            }

            if (!empty ($bind))
                $criteria->bind($bind);
        }

        return $criteria;
    }

    public static function slugify($text, $minus = false)
    {
        $r = $minus ? '-' : '_';
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', $r, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $r);

        // remove duplicate -
        $text = preg_replace('~-+~', $r, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function indexAction()
    {
        if ($this->request->isPost()) {
            $this->persistent->searchParams = $this->request->getPost();
        }

        $this->persistent->parameters = null;

        if (!is_null($this->persistent->searchParams)) {

            $default_searchParams = [];
            foreach ($this->persistent->searchParams as $key => $val) {
                if (!is_array($val)) {
                    $default_searchParams [$key] = $val;
                } else {
                    $default_searchParams [$key . '[]'] = $val;
                }
            }
            $this->tag->setDefaults($default_searchParams);
        }
    }

    public function setCurrentAppAction()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $params = $this->request->getPost();
            if (!isset($params['id_applicazione'])) {
                $this->response->setJsonContent([
                    'error'   => true,
                    'content' => 'Richiesta incompleta'
                ]);
            }

            if ($params['id_applicazione'] == '0') {
                $this->session->set('current_app', null);
            }
            if ($selectedApp = Applicazioni::findFirstById($params['id_applicazione'])) {
                $this->session->set('current_app', [
                    'id'          => $selectedApp->id,
                    'codice'      => $selectedApp->codice,
                    'titolo'      => $selectedApp->titolo,
                    'descrizione' => $selectedApp->descrizione
                ]);
                $this->response->setJsonContent([
                    'success' => true
                ]);
            } else {

                $this->response->setJsonContent([
                    'error'   => true,
                    'content' => 'Richiesta Fallita'
                ]);

            }
            return $this->response;
        }
        return $this->response->redirect('/admin/');
    }

    protected function initialize()
    {

        if ($this->di->getConfig()->debug->apc) apcu_clear_cache();

        $this->controllerName = $this->router->getModuleName() . '/' . $this->router->getControllerName();

        // Prepend the application name to the title
        $this->tag->prependTitle('CMS.IO - ');
        // $this->view->setTemplateAfter('main');

        if ($this->request->isAjax()) {
            // disattiva la renderizzazione della view
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

            // setta il content type a json
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setJsonContent([]);
        }

        $this->alert_messagge ['notFound'] = 'Non trovato!';

        $this->alert_messagge ['successCreate'] = 'Creato con successo! Svuota la cache per visualizzare le modifiche.';
        $this->alert_messagge ['failCreate'] = 'Errore creazione!';

        $this->alert_messagge ['successUpdate'] = 'Aggiornato con successo! Svuota la cache per visualizzare le modifiche.';
        $this->alert_messagge ['failUpdate'] = 'Errore aggiornamento!';

        $this->alert_messagge ['successDelete'] = 'Eliminato con successo! Svuota la cache per visualizzare le modifiche.';
        $this->alert_messagge ['failDelete'] = 'Errore eliminazione!';

        $this->alert_messagge ['failOperation'] = 'Hai effettuato una richiesta non valida!';

        if ($this->session->has('auth-identity')) {
            $this->auth = $this->session->get('auth-identity');
            $this->view->header_alert = $this->render_header_alert();
            $this->appPicker();
        }

        $this->view->additiveJs = false;
        $this->view->additiveCss = false;
        $this->view->application = $this->config->application;
        $this->assets->collection('jsAdminTheme')
            ->setTargetPath('assets/admin/js/min/admin-theme.js')
            ->setTargetUri('assets/admin/js/min/admin-theme.js')
            ->addJs('assets/admin/js/moment.min.js')
            ->addJs('assets/admin/plugins/iCheck/icheck.min.js')
            ->addJs('assets/admin/plugins/fastclick/fastclick.min.js')
            ->addJs('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')
            ->addJs('assets/admin/plugins/daterangepicker/daterangepicker.js')
            ->addJs('assets/admin/plugins/sweetalert/sweetalert.min.js')
            ->addJs('assets/admin/plugins/pace/pace.js')
            ->addJs('assets/admin/plugins/jQueryPostMessage/jquery.ba-postmessage.min.js')
            ->addJs('assets/admin/js/typeahead.jquery.min.js')
            ->addJs('assets/admin/js/jqgrid/jquery.jqGrid.min.js')
            ->addJs('assets/admin/js/jqgrid/i18n/grid.locale-it.js')
            ->addJs('assets/admin/js/bootstrap-datepicker/bootstrap-datepicker.min.js')
            ->addJs('assets/admin/js/bootstrap-datepicker/locales/bootstrap-datepicker.it.min.js')
            ->addJs('assets/admin/js/bootstrap-select/bootstrap-select.min.js')
            ->addJs('assets/admin/js/bootstrap-select/i18n/defaults-it_IT.min.js')
            ->join(true)
            ->addFilter(new Phalcon\Assets\Filters\Jsmin ());

        $this->assets->collection('cssAdminTheme')
            ->setTargetPath('assets/admin/css/min/admin-theme.css')
            ->setTargetUri('assets/admin/css/min/admin-theme.css')
            ->addCss('assets/admin/css/jqgrid/jqgrid-custom.css')
            ->addCss('assets/admin/css/AdminLTE/AdminLTE.min.css')
            ->addCss('assets/admin/css/AdminLTE/skins/skin-green.min.css')
            ->addCss('assets/admin/plugins/daterangepicker/daterangepicker.css')
            ->addCss('assets/admin/plugins/sweetalert/sweetalert.css')
            ->addCss('assets/admin/plugins/pace/pace.css')
            ->addCss('assets/admin/css/bootstrap-datepicker/bootstrap-datepicker.min.css')
            ->addCss('assets/admin/css/bootstrap-select/bootstrap-select.min.css')
            ->addCss('assets/admin/plugins/iCheck/square/green.css')
            ->join(true)
            ->addFilter(new Phalcon\Assets\Filters\Cssmin ());

    }

    protected function render_header_alert()
    {
        if (!$this->session->has('acl'))
            return [];

        $this->acl = $this->session->get('acl');
        $header_alert = [];


        if ($this->acl->isAllowed($this->auth ['ruolo'] . '[' . $this->auth ['livello'] . ']', 'index', 'rebuildIndex')) {
            $option = Options::findFirstByOptionName('reindex_queue');
            $reindex = [];
            if ($option) {
                $reindex = json_decode($option->option_value, true);
            }
            if (!empty($reindex) && is_array($reindex)) {
                $header_alert['rebuild_index'] = count($reindex);
            } else {
                $header_alert['rebuild_index'] = '0';
            }
        }
        return $header_alert;
    }

    protected function appPicker()
    {
        $this->view->appPickerActive = $this->config->application->multisite;
        if(!$this->config->application->multisite) return;

        $this->view->availableApps = $apps = self::getAvailableApps();

        if ($this->session->has('current_app')) {
            $this->view->currentApp = $this->session->get('current_app');
        } else {
            $this->session->set('current_app', null);
        }
    }

    protected static function getAvailableApps()
    {
        return Applicazioni::find([
            'conditions' => 'attivo = 1 AND id != 0',
            'cache'      => [
                'key'      => 'AdminAppList',
                'lifetime' => 32400
            ],
            'order'      => 'id ASC'
        ]);
    }

    protected function checkVincoli(Model $model)
    {

        // verfica vincoli di assegnazione per edit e delete
        if (in_array($this->controllerName, $this->auth ['vincoli_assegnazioni'])) {

            if (empty ($model->id_assegnazione_gruppo)) {
                $this->flashSession->error($this->alert_messagge ['failOperation']);
                return $this->response->redirect($this->controllerName . '/index');
            }

            $query = $model::query()->columns('id_assegnazione_gruppo')->innerJoin('Gruppi', 'g.id = ' . $model->id_assegnazione_gruppo, 'g')->innerJoin('GruppiUtenti', 'gu.id_gruppo = g.id AND gu.id_utente = ' . $this->auth ['id'], 'gu')->execute();

            if (empty ($query->toArray())) {
                $this->flashSession->error($this->alert_messagge ['failOperation']);
                return $this->response->redirect($this->controllerName . '/index');
            }
        }
    }

    protected function jqGrid_init($entityId, $entityCaption, $columns, $columns_select_editoptions = [], $parent_model_columns = ['id', 'descrizione'])
    {
        if (!empty ($columns_select_editoptions)) {
            foreach ($columns_select_editoptions as $key => $model) {
                if (isset($parent_model_columns) && is_array($parent_model_columns) && count($parent_model_columns) == 2) {
                    $id = $parent_model_columns[0] . ' AS id';
                    $desc = $parent_model_columns[1] . ' AS descrizione';
                    $col_param = $id . ',' . $desc;
                } else {
                    $col_param = 'id,descrizione';
                }

                $this->view->{$model} = $model::find([
                    'condition' => 'attivo = 1',
                    'columns'   => $col_param,
                    'cache'     => [
                        'key'      => $model . '-find',
                        'lifetime' => 86400
                    ]
                ]);

                $arr_list = [];
                foreach ($this->view->{$model} as $ts) {
                    $arr_list [] = $ts->id . ':' . $ts->descrizione;
                }
                $jqGrid_list [$model] = implode(';', $arr_list);
            }
        }

        $count = count($columns);
        for ($i = 0; $i < $count; $i++) {
            if (isset ($columns [$i] ['editable']) && $columns [$i] ['editable'] == true) {
                $this->grid_inline_editable [] = $columns [$i] ['name'];
                if (array_key_exists($columns [$i] ['name'], $columns_select_editoptions)) {
                    $columns [$i] ['editoptions'] = [
                        'value' => $jqGrid_list [$columns_select_editoptions [$columns [$i] ['name']]]
                    ];
                }
            }
        }

        $jqGrid = [
            'entityId'      => str_replace('/', '_', $entityId),
            'entityCaption' => $entityCaption,
            'gridActions'   => $this->getGridPermission($this->router->getControllerName()),
            'entityActions' => [
                'search_url' => $this->url->get($entityId . '/search'),
                'clone_url'  => $this->url->get($entityId . '/clone'),
                'edit_url'   => $this->url->get($entityId . '/edit'),
                'save_url'   => $this->url->get($entityId . '/save'),
                'delete_url' => $this->url->get($entityId . '/delete')
            ],
            'gridColumns'   => $columns
        ];

        return $jqGrid;
    }

    protected function getGridPermission($class)
    {
        $r = [];
        if (is_null($this->acl)) {
            return $this->response->redirect('login');
        }
        foreach ($this->grid_actions as $action) {
            if ($this->acl->isAllowed($this->auth ['ruolo'] . '[' . $this->auth ['livello'] . ']', $class, $action) == Acl::ALLOW) {
                $r [] = $action;
            }
        }
        return $r;
    }

    protected function jqGridExport($items)
    {
        if (empty($items)) exit();

        $count = count($this->jqGrid_columns);
        $count_items = count($items);
        $columns = [];
        $columns_name = [];
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $columns[$i] = trim(str_replace('/t', '', $this->jqGrid_columns[$i]['label']));
            $columns_name[$i] = $this->jqGrid_columns[$i]['name'];
        }
        for ($j = 0; $j < $count_items; $j++) {
            $row = [];
            foreach ($columns_name as $name) {
                $row[] = strip_tags($items[$j]->{$name});
            }
            $rows[] = $row;
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=esportazione.csv');

        $output = fopen('php://output', 'w');

        fputcsv($output, $columns);

        $nrr = count($rows);
        for ($y = 0; $y < $nrr; $y++) {
            fputcsv($output, $rows[$y]);
        }
        exit();
    }

    protected function history_back($uri = null)
    {
        $referer = $this->request->getHTTPReferer();

        if (!empty ($referer)) {
            $parse = parse_url($referer);
            $uri = ltrim($parse ['path'], '/');
        }

        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);

        return $uriParts [0] . ' -> ' . $uriParts [1];
        // return $this->dispatcher->forward(array('controller'=>$uriParts[0], 'action'=>$uriParts[1], 'params'=>$params));
    }

    protected function addLibraryAssets($libraries, $name)
    {
        $js_assets = [];
        $css_assets = [];
        $nr = count($libraries);
        for ($i = 0; $i < $nr; $i++) {

            switch ($libraries [$i]) {
                case 'jQueryValidation' :
                    $js_assets [] = 'assets/admin/plugins/jQueryValidation/jquery.validate.min.js';
                    break;
                case 'jQueryMask' :
                    $js_assets [] = 'assets/admin/plugins/jQueryMask/jquery.mask.min.js';
                    break;
                case 'jQueryPostMessage' :
                    /**
                     * @deprecated Aggiunto a loader di default
                     */
                    //$js_assets [] = 'assets/admin/plugins/jQueryPostMessage/jquery.ba-postmessage.min.js';
                    break;
                case 'holdOn' :
                    $js_assets [] = 'assets/admin/plugins/holdOn/HoldOn.min.js';
                    $css_assets [] = 'assets/admin/assets/admin/plugins/holdOn/HoldOn.min.css';
                    break;
                case 'dataTables' :
                    $js_assets [] = 'assets/admin/plugins/datatables/jquery.dataTables.min.js';
                    $js_assets [] = 'assets/admin/plugins/datatables/jquery.dataTables.min.js';
                    $js_assets [] = 'assets/admin/plugins/datatables/dataTables.bootstrap.js';
                    $css_assets [] = 'assets/admin/plugins/datatables/dataTables.bootstrap.css';
                    break;
                case 'jQueryFileUpload' :
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/vendor/jquery.ui.widget.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/load-image.all.min.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/canvas-to-blob.min.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/jquery.iframe-transport.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/cors/jquery.xdr-transport.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/jquery.fileupload.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/jquery.fileupload-process.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/jquery.fileupload-image.js';
                    $js_assets [] = 'assets/admin/plugins/jQueryFileUpload/js/jquery.fileupload-validate.js';
                    $css_assets [] = 'assets/admin/plugins/jQueryFileUpload/css/jquery.fileupload.css';
                    $css_assets [] = 'assets/admin/plugins/jQueryFileUpload/css/dropzone.css';
                    break;
                case 'bootstrapWysihtml5' :
                    $js_assets [] = 'assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js';
                    $css_assets [] = 'assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css';
                    break;
                case 'select2' :
                    $js_assets [] = 'assets/admin/plugins/select2/select2.full.min.js';
                    $css_assets [] = 'assets/admin/plugins/select2/select2.min.css';
                    break;
                case 'chartjs' :
                    $js_assets [] = 'assets/admin/plugins/chartjs/Chart.min.js';
                    break;
                case 'knob' :
                    $js_assets [] = 'assets/admin/plugins/knob/jquery.knob.js';
                    break;
                case 'highlight' :
                    $js_assets [] = 'assets/admin/plugins/highlight/highlight.pack.js';
                    $css_assets [] = 'assets/admin/plugins/highlight/styles/androidstudio.css';
                    break;
                case 'codemirror' :
                    $css_assets [] = 'assets/admin/css/codemirror/codemirror.css';
                    $css_assets [] = 'assets/admin/css/codemirror/addon/hint/show-hint.css';
                    $css_assets [] = 'assets/admin/css/codemirror/theme/dracula.css';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/hint/show-hint.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/hint/xml-hint.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/hint/html-hint.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/hint/javascript-hint.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/hint/css-hint.js';
                    $js_assets [] = 'assets/admin/js/codemirror/mode/xml/xml.js';
                    $js_assets [] = 'assets/admin/js/codemirror/mode/javascript/javascript.js';
                    $js_assets [] = 'assets/admin/js/codemirror/mode/css/css.js';
                    $js_assets [] = 'assets/admin/js/codemirror/mode/htmlmixed/htmlmixed.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/fold/xml-fold.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/edit/matchtags.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/edit/closebrackets.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/edit/closetag.js';
                    $js_assets [] = 'assets/admin/js/codemirror/addon/display/fullscreen.js';
                    break;
                case 'lazyload' :
                    $js_assets [] = 'assets/admin/js/jquery-lazyload/jquery.lazy.min.js';
                    break;
                case 'fileBrowser' :
                    $js_assets [] = 'assets/admin/plugins/filesBrowser/js/script.js';
                    $css_assets [] = 'assets/admin/plugins/filesBrowser/css/style.css';
                    break;
                default :
                    break;
            }
        }


        if (!empty ($js_assets)) {
            $r = $this->assets->collection('additiveJs')->setTargetPath('assets/admin/js/min/' . $name . '.js')->setTargetUri('assets/admin/js/min/' . $name . '.js');
            $nr = count($js_assets);
            for ($i = 0; $i < $nr; $i++) {
                $r->addJs($js_assets [$i]);
            }
            $r->join(true)->addFilter(new Phalcon\Assets\Filters\Jsmin ());
            $this->view->additiveJs = true;
        }
        if (!empty ($css_assets)) {
            $r = $this->assets->collection('additiveCss')->setTargetPath('assets/admin/css/min/' . $name . '.css')->setTargetUri('assets/admin/css/min/' . $name . '.css');
            $nr = count($css_assets);
            for ($i = 0; $i < $nr; $i++) {
                $r->addCss($css_assets [$i]);
            }
            $r->join(true)->addFilter(new Phalcon\Assets\Filters\Cssmin ());
            $this->view->additiveCss = true;
        }
    }

    protected function cacheKeyExists($key, $prefix = false)
    {
        $cache = $this->di->getModelsCache();

        if ($prefix) {
            $cache_keys = $cache->queryKeys('cmsio-cache-');
            foreach ($cache_keys as $k) {
                if (strpos($k, 'cmsio-cache-' . $key) !== false)
                    return true;
            }
            return false;
        } else {
            return $cache->exists('cmsio-cache-' . $key);
        }
    }

    protected function cacheKeyFlush($key, $prefix = false)
    {
        $cache = $this->di->getModelsCache();

        if ($prefix) {
            $cache_keys = $cache->queryKeys('cmsio-cache-');
            foreach ($cache_keys as $k) {
                if (strpos($k, 'cmsio-cache-' . $key) !== false) {
                    $cache->delete(str_replace('cmsio-cache-', '', $k));
                }
            }
        } else {
            if ($cache->exists('cmsio-cache-' . $key)) {
                $cache->delete($key);
            }
        }
    }

    protected function beginTransaction()
    {
        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        return $manager->get();
    }
}
