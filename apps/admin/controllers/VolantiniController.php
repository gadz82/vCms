<?php

use apps\admin\forms\volantini as VolantiniForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class VolantiniController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Volantino');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Volantino non trovato!';

        $this->alert_messagge['successCreate'] = 'Volantino creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione volantino!';

        $this->alert_messagge['successUpdate'] = 'Volantino aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento volantino!';

        $this->alert_messagge['successDelete'] = 'Volantino eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione volantino!';

        $this->jqGrid_columns = [
            [
                'label'     => 'Nr.Promo',
                'name'      => 'numero',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Regione',
                'name'      => 'id_regione',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Tipo Insegna',
                'name'      => 'id_tipologia_punto_vendita',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Stato',
                'name'      => 'id_tipologia_stato',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Tipo',
                'name'      => 'id_tipologia_volantino',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],

            [
                'label' => 'PDV',
                'name'  => 'id_punto_vendita'
            ],
            [
                'label'     => 'Titolo',
                'name'      => 'titolo',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],

            [
                'label'     => 'Anno',
                'name'      => 'anno',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Data Inizio',
                'name'      => 'data_inizio_pubblicazione',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Data Fine',
                'name'      => 'data_fine_pubblicazione',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ]
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [
            'id_tipologia_stato'         => 'TipologieStatoVolantino',
            'id_tipologia_volantino'     => 'TipologieVolantino',
            'id_tipologia_punto_vendita' => 'TipologiePuntoVendita',
            'id_regione'                 => 'Regioni'
        ];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Volantino', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new VolantiniForms\IndexForm();
        $this->view->form = $form;

        $this->assets->addJs('assets/admin/js/grid.js');

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

                $query = self::fromInput($this->di, 'Volantini', $search);
                $query->andWhere('Volantini.attivo = 1');

                $query->innerJoin('TipologieStatoVolantino', 'ts.id = Volantini.id_tipologia_stato AND ts.attivo = 1', 'ts');
                $query->innerJoin('TipologieVolantino', 'tv.id = Volantini.id_tipologia_volantino AND tv.attivo = 1', 'tv');
                $query->innerJoin('TipologiePuntoVendita', 'tpv.id = Volantini.id_tipologia_punto_vendita AND tpv.attivo = 1', 'tpv');
                $query->innerJoin('Regioni', 'r.id = Volantini.id_regione AND r.attivo = 1', 'r');
                $query->leftJoin('PuntiVendita', 'pv.id = Volantini.id_punto_vendita AND pv.attivo = 1', 'pv');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'Volantini.' . $sort . ' ' . $order;
                $parameters['group'] = 'Volantini.id';

                //effettua la ricerca
                $controller_data = Volantini::find($parameters);

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
                    $item->id_tipologia_stato = $item->TipologieStatoVolantino->descrizione;
                    $item->id_tipologia_volantino = $item->TipologieVolantino->descrizione;
                    $item->id_tipologia_punto_vendita = $item->TipologiePuntoVendita->descrizione;
                    $item->id_punto_vendita = $item->PuntiVendita->nome;
                    $item->id_regione = $item->Regioni->descrizione;
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

    public function newAction()
    {
        $form = new VolantiniForms\NewForm();
        $sparams = $this->getDI()->getSession()->get('sparams');
        if ($sparams) {
            $block = new Volantini();
            $form->bind($sparams, $block);
            $this->getDI()->getSession()->remove('sparams');
        }
        $this->view->form = $form;
    }

    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'index'
            ]);
        }
        $params = $this->request->getPost();
        if (!isset($params['id_punto_vendita']) || empty($params['id_punto_vendita'])) {
            $params['id_punto_vendita'] = null;
        }
        $params['data_creazione'] = date('Y-m-d H:i:s');
        $form = new VolantiniForms\NewForm();
        $Volantini = new Volantini();
        $Volantini->assign($params);

        if (!$form->isValid($params, $Volantini)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$Volantini->save()) {
            $this->flash->error($Volantini->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/edit/' . $Volantini->id);
        }
    }

    public function fileuploadAction($id_volantino)
    {
        $volantino = Volantini::findFirstById($id_volantino);
        $this->view->disable();
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $upload_path = Volantini::getVolantinoPath($volantino);
            /**
             * @var \apps\admin\library\UploadHandler $uploader
             */
            $uploader = $this->di->getUploader();
            $upload_dir = BASE_DIR . '/../public/raw/volantini/' . $upload_path;
            $upload_url = $this->config->application->protocol . $this->config->application->siteUri . '/raw/volantini/' . $upload_path;

            $uploader->setOptions([
                'upload_dir'       => $upload_dir,
                'upload_url'       => $upload_url,
                'image_file_types' => '/\.(jpe?g|png|gif)$/i',
                'max_width'        => 2560,
                'max_height'       => 1440,
                'min_width'        => 320,
                'min_height'       => 240,
                'param_name'       => 'file',
                'image_versions'   => [],
                'orginal_filename' => true
            ], true, null);

            $rs = $uploader->get_response();

            $f = $rs['file'][0];
            if (!isset($f->url)) return;
            if (isset($f->error)) {
                return;
            }

        }
    }

    public function editAction($id)
    {
        $controller_data = Volantini::findFirstById($id);
        $original = clone $controller_data;
        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new VolantiniForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa è in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();
            if (!isset($params['id_punto_vendita']) || empty($params['id_punto_vendita'])) {
                $params['id_punto_vendita'] = null;
            }

            if ($form->isValid($params)) {

                $controller_data->assign($params);

                if ($controller_data->save()) {
                    $this->flashSession->success($this->alert_messagge['successUpdate']);

                    return $this->response->redirect($this->controllerName . '/edit/' . $controller_data->id);
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

        $upload_path = Volantini::getVolantinoPath($controller_data);
        $upload_dir = BASE_DIR . '/../public/raw/volantini/' . $upload_path;
        $this->view->upload_url = $this->config->application->protocol . $this->config->application->siteUri . '/raw/volantini/' . $upload_path;
        $files = false;
        if (file_exists($upload_dir)) {
            $files = array_filter(scandir($upload_dir), function ($item) use ($upload_dir) {
                return !is_dir($upload_dir . $item);
            });
        }
        $this->view->files = $files;
        $this->view->id = $id;
        $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
        $this->view->form = $form;
        $this->view->controller_data = $controller_data;
        $this->addLibraryAssets(['jQueryValidation', 'jQueryFileUpload'], $this->controllerName . '-edit');
        $this->assets->addJs('assets/admin/js/volantini/volantini-files.js');
        $this->assets->addJs('assets/admin/js/volantini/edit.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');
    }

    public function saveAction()
    {
        /*
         * Verifica se la richiesa è in POST (richiesta update)
         */
        if ($this->request->isPost() && $this->request->isAjax()) {

            $params = $this->request->getPost();
            $controller_data = Volantini::findFirstById($params['id']);
            $original = clone $controller_data;
            if (!$controller_data) {
                $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
            }

            $controller_data->assign($params);

            if ($controller_data->save()) {
                $this->response->setJsonContent(['success' => $this->alert_messagge['successUpdate']]);
            } else {
                $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
            }

        } else {
            $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
        }
        return $this->response;
    }

    public function cloneAction($id)
    {
        $controller_data = Volantini::findFirstById($id);
        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $sparams = $controller_data->toArray();
        $this->getDI()->getSession()->set('sparams', $sparams);
        return $this->response->redirect($this->controllerName . '/new');
    }

    public function trashImagesAction($id)
    {
        $volantino = Volantini::findFirstById($id);
        if (!$volantino) {
            $this->flashSession->error('Impossibile eliminare immagini volantino');
            return $this->response->redirect($this->controllerName . '/edit/' . $id);
        }
        $del = Volantini::trashImages($volantino);

        if (!$del) {
            $this->flashSession->error('Impossibile eliminare immagini volantino');
            return $this->response->redirect($this->controllerName . '/edit/' . $id);
        }

        return $this->response->redirect($this->controllerName . '/edit/' . $id . '#box-content-upload');
    }


    public function deleteAction($id)
    {

        $controller_data = Volantini::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->response->redirect($this->controllerName . '/index');
        }

        if (!$controller_data->delete()) {
            foreach ($controller_data->getMessages() as $message) {
                $this->flashSession->error($message);
                return $this->response->redirect($this->controllerName . '/index');
            }
        } else {
            $dir = Volantini::getVolantinoPath($controller_data);
            array_map('unlink', glob($dir . '{,.}*', GLOB_BRACE));
            rmdir($dir);
        }

        $this->flashSession->success($this->alert_messagge['successDelete']);

        return $this->response->redirect($this->controllerName . '/index');

    }


}
