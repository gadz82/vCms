<?php

use apps\admin\forms\files as FilesForms;

use Phalcon\Paginator\Adapter\Model as Paginator;
use app\library\UploadHandler;

class FilesController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('File');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'File non trovato!';

        $this->alert_messagge['successCreate'] = 'File creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione file!';

        $this->alert_messagge['successUpdate'] = 'File aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento file!';

        $this->alert_messagge['successDelete'] = 'File eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione file!';

        $this->jqGrid_columns = [
            ['label' => 'Nome File', 'name' => 'filename'],
            ['label' => 'Tipo File', 'name' => 'filetype'],
            ['label' => 'Alt', 'name' => 'alt'],
            ['label' => 'Data', 'name' => 'data_creazione'],
            ['label' => 'Url', 'name' => 'fileurl']
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'File', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new FilesForms\IndexForm();
        $this->view->form = $form;

        $this->assets->addJs('assets/admin/js/grid.js');
        $this->assets->addJs('assets/admin/js/files/index.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');

    }

    public function searchAction()
    {

        if ($this->request->isPost() || $this->request->hasPost('export')) {
            if ($this->request->isAjax() || $this->request->hasPost('export')) {

                if ($this->request->hasPost('form_search')) {
                    $data = $this->request->getPost('form_search');
                    parse_str($data, $search);
                } else {
                    $search = $this->request->getPost();
                }

                $query = self::fromInput($this->di, 'Files', $search);
                $query->andWhere('Files.attivo = 1');

                $query->innerJoin('TipologieStatoFile', 'ts.id = Files.id_tipologia_stato AND ts.attivo = 1', 'ts');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                if ($this->request->hasPost('sort') && $this->request->getPost('sort') == 'azioni' && $this->request->hasPost('files') && !empty($this->request->getPost('files'))) {
                    $files = $this->request->getPost('files');

                    if (is_array($files)) {
                        $sort = 'FIELD(Files.id,' . implode(',', $files) . ') ' . $order . ', Files.id ' . $order;
                    } else {
                        $sort = 'FIELD(Files.id,' . $files . ') ' . $order . ', Files.id ' . $order;
                    }
                    $parameters ['order'] = $sort;
                } else {
                    if ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) {
                        $sort = 'Files.' . $this->request->getPost('sort') . ' ' . $order;
                    } else {
                        if ($this->request->hasPost('files') && !empty($this->request->getPost('files'))) {
                            $files = $this->request->getPost('files');
                            if (is_array($files)) {
                                $sort = 'FIELD(Files.id,' . implode(',', $files) . ') ' . $order . ', Files.id ' . $order;
                            } else {
                                $sort = 'FIELD(Files.id,' . $files . ') ' . $order . ', Files.id ' . $order;
                            }
                        } else {
                            $sort = 'Files.id ' . $order;
                        }
                    }

                    $parameters ['order'] = $sort;
                }

                $parameters['group'] = 'Files.id';

                //effettua la ricerca
                $controller_data = Files::find($parameters);

                if ($controller_data->count() == 0) return $this->response;

                //crea l'oggetto paginator
                if ($this->request->hasPost('export')) {
                    $paginator = new Paginator(['data' => $controller_data, 'limit' => 65000, 'page' => 1]);
                } else {
                    $paginator = new Paginator([
                        'data'  => $controller_data,
                        'limit' => ($this->request->hasPost('rows') && !empty($this->request->getPost('rows'))) ? $this->request->getPost('rows') : 20,
                        'page'  => ($this->request->hasPost('page') && !empty($this->request->getPost('page'))) ? $this->request->getPost('page') : 1,
                    ]);
                }

                $now = new DateTime(date('Y-m-d'));
                $paging = $paginator->getPaginate();
                foreach ($paging->items as $item) {
                    $item->id_tipologia_stato = $item->TipologieStatoFile->descrizione;
                    //$item->fileurl = '<a href="#" id="image-zoom-'.$item->id.'" data-image-zoom="'.$item->fileurl.'">Visualizza <i class="fa fa-search fa-fw"></i></a>';
                    if ($item->private) {
                        if ($item->filetype !== 'application/pdf') {
                            $img_zoom = "/media/render/" . $item->filename;
                            $img_thumb = "/media/render/" . $item->filename . "?size=thumbnail";
                            $item->fileurl = '<div class="text-center"><a href="#" id="image-zoom-' . $item->id . '" data-image-zoom="' . $img_zoom . '"><img src="' . $img_thumb . '"></a></div>';
                        } else {
                            $item->fileurl = '<div class="text-center"><a href="/media/render/' . $item->filename . '" target="_blank"><span class="fa fa-file-text-o fa-fw fa-3x text-light-blue"></span></a></div>';
                        }
                        $item->alt .= ' | <b>PRIVATO</b>';
                    } else {
                        if ($item->filetype !== 'application/pdf') {
                            $img_zoom = "/files/" . $item->filename;
                            $img_thumb = "/files/thumbnail/" . $item->filename;
                            $item->fileurl = '<div class="text-center"><a href="#" id="image-zoom-' . $item->id . '" data-image-zoom="' . $img_zoom . '"><img src="' . $img_thumb . '"></a></div>';
                        } else {
                            $item->fileurl = '<div class="text-center"><a href="/files/' . $item->filename . '" target="_blank"><span class="fa fa-file-text-o fa-fw fa-3x text-light-blue"></span></a></div>';

                        }
                    }
                }

                if ($this->request->hasPost('export')) {
                    //crea un file excel con il risultato della ricerca
                    $this->jqGridExport($paging->items);
                } else {
                    //crea l'array grid da passare a jqgrid
                    $grid = ['records' => $paging->total_items, 'page' => $paging->current, 'total' => $paging->total_pages, 'rows' => $paging->items];

                    $this->response->setJsonContent($grid);
                    return $this->response;
                }
            }
        }

        return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);

    }

    public function getFileListAction()
    {

        if ($this->request->isAjax() == true) {
            $files = Files::find([
                'attivo = 1',
                'order' => 'id DESC, priorita DESC'
            ]);

            if (!$files) {
                $this->response->setJsonContent(['error' => $this->alert_messagge['notFound']]);
                return $this->response;
            }

            $rs = $this->view->getRender('files', 'fileListAjax', ['files_list' => $files], function ($view) {
                $view->setViewsDir("../apps/admin/views/partials/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
            $this->response->setJsonContent(['success' => 'success', 'data' => $rs]);
            return $this->response;
        }

        $this->response->setJsonContent(['error' => 'error']);
        return $this->response;

    }

    public function iframeFileManagerAction()
    {

        parent::indexAction();
        $this->grid_actions = [
            'select',
            'edit'
        ];


        $auth = $this->session->get('auth-identity');
        $acl = \apps\admin\plugins\SecurityPlugin::getIstance()->getAcl();

        if ($acl->isAllowed($auth ['ruolo'] . '[' . $auth ['livello'] . ']', 'files', 'delete') == Phalcon\Acl::ALLOW) {
            $this->grid_actions[] = 'delete';
        }

        $jqGrid_select_editoptions = [];
        $this->view->baseUrl = $this->config->application->protocol . $this->config->application->siteUri . '/files/';
        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'File', $this->jqGrid_columns, $jqGrid_select_editoptions);
        $this->view->setMainView('blank');
        $form = new FilesForms\IframeForm();
        $this->view->form = $form;
        $this->assets->addJs('assets/admin/js/grid-file-manager.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');
    }

    public function iframeListAction()
    {

        parent::indexAction();

        $selected = [];
        if (!$this->request->get('input') || empty($this->request->get('input'))) exit();
        if (!$this->request->get('multi') || empty($this->request->get('multi'))) exit();

        if ($this->request->get('files') && !empty($this->request->get('files'))) {

            $selected = explode(',', $this->request->get('files'));
            asort($selected);
            $selected_files_id = implode(',', $selected);
            $nr = count($selected);

        }

        $this->grid_actions = [
            'select',
            'edit'
        ];

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'File', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $this->view->selected_files = $selected;
        $this->view->selected_files_var = $selected_files_id;
        $this->view->parent_input = $this->request->get('input');
        $this->view->multiple = $this->request->get('multi');
        $this->view->setMainView('blank');
        $form = new FilesForms\IframeForm();
        $this->view->form = $form;
        $this->addLibraryAssets(['jQueryPostMessage'], $this->controllerName . '-iframeList');
        $this->assets->addJs('assets/admin/js/grid-files.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');
    }

    public function iframeUploadAction()
    {
        $this->view->setMainView('blank');
        $this->addLibraryAssets(['jQueryFileUpload', 'jQueryPostMessage'], $this->controllerName . '-iframeupload');

        foreach (UsersGroups::find() as $ug) {
            $ugs[$ug->id] = $ug->titolo;
        }

        $this->view->usersGroups = json_encode($ugs, JSON_FORCE_OBJECT);
        $this->assets->addJs('assets/admin/js/files/iframeUpload.js');
    }


    public function getFileAction($id)
    {
        if ($this->request->isAjax()) {
            $file = Files::findFirst([
                'conditions' => 'id = ?1 AND attivo = 1',
                'bind'       => [1 => $id]
            ]);
            if ($file) {
                $this->response->setJsonContent(['success' => true, 'content' => $file->toArray()]);
            } else {
                $this->response->setJsonContent(['success' => false]);
            }
        } else {
            $this->response->setJsonContent(['success' => false]);
        }
        return $this->response;
    }

    public function getLastFileAction()
    {
        if ($this->request->isPost()) {
            if ($this->request->isAjax() == true) {
                $post = $this->request->getPost();
                $file_names = $post['files'];
                $files = Files::find([
                    'conditions' => 'filename IN("' . implode('","', $file_names) . '") AND attivo = 1'
                ]);
                if (!$files) {
                    $this->response->setJsonContent(['error' => $this->alert_messagge['notFound']]);
                    return $this->response;
                }

                $rs = $this->view->getRender('files', 'fileListAjax', ['files_list' => $files], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });
                $this->response->setJsonContent(['success' => 'success', 'data' => $rs]);
                return $this->response;
            }
        }
    }

    public function newAction()
    {
        $this->addLibraryAssets(['jQueryFileUpload'], $this->controllerName . '-new');

        foreach (UsersGroups::find() as $ug) {
            $ugs[$ug->id] = $ug->titolo;
        }

        $this->view->usersGroups = json_encode($ugs, JSON_FORCE_OBJECT);
        $this->assets->addJs('assets/admin/js/files/new.js');
    }

    public function createAction()
    {

        $this->view->disable();
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            /**
             * @var \apps\admin\library\UploadHandler $uploader
             */
            $uploader = $this->di->getUploader();
            $imageVersions = \apps\admin\library\ImageHandler::getIstance()->imageVersions;

            $upload_dir = BASE_DIR . '/../public/files/';
            $upload_url = $this->config->application->protocol . $this->config->application->siteUri . '/files/';
            $reserved = false;
            if (isset($params['files_users_groups']) && $params['files_users_groups'] > 0) {
                $reserved = true;
                $upload_url = $this->config->application->protocol . $this->config->application->siteUri . '/files/reserved/';
                $upload_dir = BASE_DIR . '/../public/files/reserved/';
            }

            $uploader->setOptions([
                'upload_dir'       => $upload_dir,
                'upload_url'       => $upload_url,
                'image_file_types' => '/\.(jpe?g|png|gif)$/i',
                'max_width'        => 2560,
                'max_height'       => 1440,
                'param_name'       => 'file',
                'image_versions'   => $imageVersions
            ], true, null);

            $rs = $uploader->get_response();

            $f = $rs['file'][0];
            if (!isset($f->url)) return;
            if (isset($f->error)) {
                return;
            }
            $pf = new Files();
            $pf->id_tipologia_stato = 1;
            $pf->original_filename = $f->original_name;
            $pf->filename = $f->name;
            $pf->filetype = $f->type;
            $pf->filesize = $f->size;
            $pf->filepath = !$reserved ? '/public/files/' : '/public/files/reserved/';
            $pf->fileurl = $f->url;
            $pf->alt = $params['alt'];
            $pf->private = $reserved ? '1' : '0';
            $pf->priorita = $params['priorita'];
            $pf->data_creazione = date('Y-m-d H:i:s');
            if ($pf->save()) {
                \apps\admin\library\ImageHandler::getIstance()->regenerateThumbnails($pf->id);
            }
            foreach ($pf->getMessages() as $message) {
                echo $message->__toString();
            }

            if ($reserved) {
                $files_users_groups = new FilesUsersGroups();
                $files_users_groups->id_file = $pf->id;
                $files_users_groups->id_user_group = $params['files_users_groups'];
                $files_users_groups->save();
            }
        }
    }

    public function editAction($id)
    {

        $controller_data = Files::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new FilesForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();

            if ($form->isValid($params)) {

                $reserved = false;
                $controller_data->assign($params);
                if (isset($params['filesUsersGroups']) && $params['filesUsersGroups'] > 0) {
                    $reserved = true;
                    $controller_data->private = '1';
                } else {
                    $controller_data->private = '0';
                }

                if ($controller_data->save()) {

                    $fug = FilesUsersGroups::findFirst([
                        'conditions' => 'id_file = ?1 AND attivo IN(0,1)',
                        'bind'       => [1 => $id]
                    ]);
                    if (isset($params['filesUsersGroups']) && $params['filesUsersGroups'] > 0) {
                        if ($fug) {
                            $fug->id_user_group = $params['filesUsersGroups'];
                            $fug->attivo = 1;
                        } else {
                            $fug = new FilesUsersGroups();
                            $fug->id_file = $id;
                            $fug->id_user_group = $params['filesUsersGroups'];
                            $fug->attivo = 1;
                        }
                        self::toggleFileReservedFolder($controller_data, true);
                        $fug->save();
                    } else {
                        if (isset($fug)) {
                            self::toggleFileReservedFolder($controller_data, false);
                            $fug->delete();
                        }
                    }

                    $this->flashSession->success($this->alert_messagge['successUpdate']);
                    return $this->response->redirect($this->controllerName . '/index');
                } else {
                    $message = [];
                    foreach ($controller_data->getMessages() as $message) {
                        $messages[] = $message;
                    }
                    $this->flash->error(implode(' | ', $messages));
                }

            } else {
                $message = [];
                foreach ($form->getMessages() as $message) {
                    $messages[] = $message;
                }
                $this->flash->error(implode(' | ', $messages));
            }

        }

        $sizeKeys = array_keys(self::getImageVersions());

        $anteprime = [];
        $anteprime[] = [
            'size' => 'original',
            'file' => !$controller_data->private ?
                $this->getDI()->get('config')->application->baseUri . 'files' . DIRECTORY_SEPARATOR . $controller_data->filename :
                $this->getDI()->get('config')->application->baseUri . 'media' . DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR . $controller_data->filename
        ];

        foreach ($sizeKeys as $size) {
            $anteprime[] = [
                'size' => $size,
                'file' => !$controller_data->private ?
                    $this->getDI()->get('config')->application->baseUri . 'files' . DIRECTORY_SEPARATOR . $size . DIRECTORY_SEPARATOR . $controller_data->filename :
                    $this->getDI()->get('config')->application->baseUri . 'media' . DIRECTORY_SEPARATOR . 'render' . DIRECTORY_SEPARATOR . $controller_data->filename . '?size=' . $size
            ];
        }

        $this->view->id = $id;
        $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
        $this->view->form = $form;
        $fug = FilesUsersGroups::findFirst([
            'conditions' => 'id_file = ?1',
            'bind'       => [1 => $id]
        ]);
        if ($fug) $controller_data->filesUsersGroups = $fug->id_user_group;
        $this->view->controller_data = $controller_data;
        $this->view->anteprime = $anteprime;

        $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');

        $this->assets->addJs('assets/admin/js/files/edit.js');

    }

    /**
     * Sposta un file da / verso le folders dell'area ad accesso limitato
     * @param Files $file
     * @param SplBool $toReserved
     */
    private static function toggleFileReservedFolder(Files $file, $toReserved = false)
    {
        $sizes = self::getImageVersions();
        if ($toReserved) {
            //Spostamento da area pubblica ad area riservata
            foreach ($sizes as $key => $val) {
                if (file_exists(FILES_DIR . $key . '/' . $file->filename)) {
                    rename(FILES_DIR . $key . '/' . $file->filename, FILES_DIR . 'reserved/' . $key . '/' . $file->filename);
                }
            }
            //sposto file originale
            if (file_exists(FILES_DIR . '/' . $file->filename)) {
                rename(FILES_DIR . '/' . $file->filename, FILES_DIR . 'reserved/' . $file->filename);
            }
        } else {
            //Spostamento da area riservata ad area pubblica
            foreach ($sizes as $key => $val) {
                if (file_exists(FILES_DIR . 'reserved/' . $key . '/' . $file->filename)) {
                    rename(FILES_DIR . 'reserved/' . $key . '/' . $file->filename, FILES_DIR . $key . '/' . $file->filename);
                }
            }
            //sposto file originale
            if (file_exists(FILES_DIR . 'reserved/' . $file->filename)) {
                rename(FILES_DIR . 'reserved/' . $file->filename, FILES_DIR . '/' . $file->filename);
            }
        }
    }

    public static function getImageVersions()
    {
        $filesSizes = FilesSizes::find();

        $imageVersions = ['thumbnail' => [
            'max_width'  => 80,
            'max_height' => 80
        ]];

        foreach ($filesSizes as $fsize) {
            $imageVersions[$fsize->key] = [
                'crop'       => $fsize->crop == '1' ?: false,
                'max_width'  => $fsize->max_width,
                'max_height' => $fsize->max_height
            ];
        }
        return $imageVersions;
    }

    public function updateFileInfoAction($id)
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) {
            $this->response->redirect($this->controllerName . '/index');
            return false;
        }

        $params = $this->request->getPost();
        if (!isset($params['key']) || !isset($params['value'])) {
            $this->response->setStatusCode(500);
            $this->response->setJsonContent(['error' => true, 'data' => 'missing_params']);
            return $this->response;
        }

        $file = Files::findFirstById($id);

        if (!$file) {
            $this->response->setStatusCode(500);
            $this->response->setJsonContent(['error' => true, 'data' => 'file not found']);
        } else {
            if ($params['key'] == 'alt') $file->alt = $params['value'];
            if ($params['key'] == 'priorita') $file->priorita = $params['value'];
            if ($file->save()) {
                $this->response->setJsonContent(['success' => true]);
            } else {
                $this->response->setStatusCode(500);
                $this->response->setJsonContent(['error' => true, 'data' => $file->getMessages()]);
            }
        }
        return $this->response;
    }

    public function saveAction()
    {

    }

    public function deleteAction($id)
    {

        $controller_data = Files::findFirstById($id);

        if ($this->request->isAjax()) {
            $this->view->disable();
            if (!$controller_data) {
                $this->response->setJsonContent(['success' => false]);
                return $this->response;
            }
            if (!$controller_data->delete()) {
                $this->response->setJsonContent(['success' => false]);
            } else {
                $this->response->setJsonContent(['success' => true]);
            }
            return $this->response;
        }

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->response->redirect($this->controllerName . '/index');
        } else {
            $sizes = self::getImageVersions();
            foreach ($sizes as $key => $val) {
                if (file_exists(FILES_DIR . $key . '/' . $controller_data->filename)) {
                    unlink(FILES_DIR . $key . '/' . $controller_data->filename);
                }
                if (file_exists(FILES_DIR . 'reserved/' . $key . '/' . $controller_data->filename)) {
                    unlink(FILES_DIR . 'reserved/' . $key . '/' . $controller_data->filename);
                }
            }
            if (file_exists(FILES_DIR . $controller_data->filename)) {
                unlink(FILES_DIR . $controller_data->filename);
            }
            if (file_exists(FILES_DIR . 'reserved/' . $controller_data->filename)) {
                unlink(FILES_DIR . 'reserved/' . $controller_data->filename);
            }
        }

        if (!$controller_data->delete()) {
            foreach ($controller_data->getMessages() as $message) {
                $this->flashSession->error($message);
                return $this->response->redirect($this->controllerName . '/index');
            }
        }

        $files_users_groups = FilesUsersGroups::find([
            'conditions' => 'id_file = ?1',
            'bind'       => [1 => $id]
        ]);

        foreach ($files_users_groups as $fug) {
            $fug->delete();
        }

        $this->flashSession->success($this->alert_messagge['successDelete']);
        return $this->response->redirect($this->controllerName . '/index');

    }

    public function regenerateAllThumbsAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            $filter = null;
            if (isset($post['key'])) {
                $filter = $post['key'];
            }
            $files = Files::find();
            $success_count = 0;
            $errors = [];
            ini_set('max_execution_time', 0);
            foreach ($files as $file) {
                if (!\apps\admin\library\ImageHandler::getIstance()->regenerateThumbnails($file, $filter)) {
                    $errors[] = "Errore Generazione Thumbnails per file id : " . $file->id;
                } else {
                    $success_count++;
                }
            }
            $this->flashSession->success($success_count . ' immagini rigenerate');
            if (!empty($errors)) {
                $this->flashSession->error(implode(PHP_EOL, $errors));
            }
        }
        $filesSizes = \FilesSizes::find();

        $imageVersions = ['thumbnail'];

        foreach ($filesSizes as $fsize) {
            $imageVersions[] = $fsize->key;
        }
        $this->view->imageVersions = $imageVersions;

    }

    public function regenerateSingleFileAction($id)
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            if (\apps\admin\library\ImageHandler::getIstance()->regenerateThumbnails($id)) {
                $this->response->setJsonContent(['success' => true]);
            } else {
                $this->response->setStatusCode(500);
            }
        } else {
            $this->response->redirect($this->controllerName . '/index');
        }
        return $this->response;
    }

    public function cleanFilesAction()
    {
        if ($this->request->isPost()) {

            $files = Files::find();

            $sizes = FilesSizes::find();

            $files_list = [];
            $reserved_files_list = [];
            foreach ($files as $file) {
                if ($file->private) {
                    $reserved_files_list[] = $file->filename;
                } else {
                    $files_list[] = $file->filename;
                }
            }
            $tot_removed = 0;
            if (!empty($files_list)) {
                $files_del = array_filter(scandir(FILES_DIR), function ($item) use ($files_list) {
                    return !is_dir(FILES_DIR . $item) && !in_array($item, $files_list);
                });
                $files_del = array_values($files_del);

                $nr = count($files_del);
                for ($i = 0; $i < $nr; $i++) {
                    foreach ($sizes as $key => $val) {
                        if (file_exists(FILES_DIR . $key . '/' . $files_del[$i])) {
                            unlink(FILES_DIR . $key . '/' . $files_del[$i]);
                        }
                    }
                    if (file_exists(FILES_DIR . $files_del[$i])) {
                        unlink(FILES_DIR . $files_del[$i]);
                        $tot_removed++;
                    }
                }
            }
            if (!empty($reserved_files_list)) {

                $reserved_files_del = array_filter(scandir(FILES_DIR . 'reserved/'), function ($item) use ($reserved_files_list) {
                    return !is_dir(FILES_DIR . $item) && !in_array($item, $reserved_files_list);
                });
                $reserved_files_del = array_values($reserved_files_del);
                $nrd = count($reserved_files_del);
                for ($x = 0; $x < $nrd; $x++) {
                    foreach ($sizes as $key => $val) {
                        if (file_exists(FILES_DIR . 'reserved/' . $key . '/' . $reserved_files_del[$x])) {
                            unlink(FILES_DIR . 'reserved/' . $key . '/' . $reserved_files_del[$x]);
                        }
                    }

                    if (file_exists(FILES_DIR . 'reserved/' . $reserved_files_del[$x])) {
                        unlink(FILES_DIR . 'reserved/' . $reserved_files_del[$x]);
                        $tot_removed++;
                    }
                }
            }
            $this->flash->success('Pulizia file terminata, eliminati ' . $tot_removed . ' files');

        }

    }

    public function getEmbedCodeAction($fileId)
    {
        if (!$this->request->isAjax()) return $this->response->redirect($this->controllerName . '/index');

        $file = Files::findFirstById($fileId);

        if (!$file) {
            return $this->response->setJsonContent(['success' => false]);
        }
        $url = $file->private ? $this->config->application->baseUri . 'media/render/' . $file->filename : $this->config->application->baseUri . 'files/' . $file->filename;
        $links = "";
        if (substr($file->filetype, 0, 5) === "image") {
            $fileSizes = self::getImageVersions();
            $links .= '
                <div class="clearfix">
                    <strong>Embed HTML</strong><br>
                    <pre>' . htmlentities('<img src="' . $url . '" alt="' . $file->alt . '">') . PHP_EOL;
            foreach ($fileSizes as $size => $settings) {
                $url = $file->private ? $this->config->application->baseUri . 'media/render/' . $file->filename . '?size=' . $size : $this->config->application->baseUri . 'files/' . $size . '/' . $file->filename;
                $links .= htmlentities('<img src="' . $url . '" alt="' . $file->alt . '">') . PHP_EOL;
            }
            $links .= '</pre></div>';

            $links .= '</div>
            <div class="clearfix">
                <strong>Embed Shortcode</strong><br>
                <pre>[[renderImage||' . $file->id . ']]' . PHP_EOL;
            foreach ($fileSizes as $size => $settings) {
                $links .= '[[renderImage||' . $file->id . ',' . $size . ']]' . PHP_EOL;
            }
            $links .= '</pre>
                </div>
            ';

        } else {
            $links .= '
                <div class="clearfix">
                    <strong>Embed HTML</strong><br>
                    <pre>' . htmlentities('<a href="' . $url . '" class="button" title="' . $file->alt . '" download>Scarica</a>') . PHP_EOL . '</pre></div>';

            $links .= '</div>
                <div class="clearfix">
                    <strong>Embed Shortcode</strong><br>
                    <pre>[[downloadButton||' . $file->id . ']]' . PHP_EOL . '</pre>
                </div>';
        }


        return $this->response->setJsonContent(['success' => true, 'content' => $links]);

    }

}
