<?php

use apps\admin\forms\blocks as BlocksForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class BlocksController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Block');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Block non trovato!';

        $this->alert_messagge['successCreate'] = 'Block creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione block!';

        $this->alert_messagge['successUpdate'] = 'Block aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento block!';

        $this->alert_messagge['successDelete'] = 'Block eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione block!';

        $this->jqGrid_columns = [
            ['label' => 'App', 'name' => 'id_applicazione'],
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
                'label'     => 'Key',
                'name'      => 'key',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Stato block',
                'name'      => 'id_tipologia_stato',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Block Tag',
                'name'      => 'id_block_tag',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            ['label' => 'Tipo block', 'name' => 'id_tipologia_block'],
            ['label' => 'Data Pubblicazione', 'name' => 'data_inizio_pubblicazione'],
            ['label' => 'Data Fine Pubblicazione', 'name' => 'data_fine_pubblicazione'],
        ];

    }

    public function indexAction()
    {

        parent::indexAction();
        $jqGrid_select_editoptions = [
            'id_tipologia_stato' => 'TipologieStatoBlock',
            'id_block_tag'       => 'BlocksTags'
        ];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Block', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new BlocksForms\IndexForm();
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

                $query = self::fromInput($this->di, 'Blocks', $search);
                $query->andWhere('Blocks.attivo = 1');

                $query->innerJoin('TipologieStatoBlock', 'ts.id = Blocks.id_tipologia_stato AND ts.attivo = 1', 'ts');
                $query->innerJoin('BlocksTags', 'bt.id = Blocks.id_block_tag AND bt.attivo = 1', 'bt');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'Blocks.' . $sort . ' ' . $order;
                $parameters['group'] = 'Blocks.id';

                //effettua la ricerca
                $controller_data = Blocks::find($parameters);

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
                    $item->id_tipologia_stato = $item->TipologieStatoBlock->descrizione;
                    $item->id_tipologia_block = $item->TipologieBlock->descrizione;
                    $item->id_block_tag = $item->BlocksTags->descrizione;
                    $item->id_applicazione = $item->Applicazioni->descrizione;
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
        /**
         * @TODO Editor per syntax highlight e autocompletion
         */
        $this->assets->addJs('assets/admin/js/codemirror/codemirror.js');
        $this->addLibraryAssets(['codemirror'], $this->controllerName . '-new');
        $this->assets->addJs('assets/admin/js/blocks/text-editor.js');
        $form = new BlocksForms\NewForm();
        $sparams = $this->getDI()->getSession()->get('sparams');
        if ($sparams) {
            $block = new Blocks();
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
        $params ['data_creazione'] = date('Y-m-d H:i:s');
        if (!isset($params ['data_inizio_pubblicazione']) || empty($params ['data_inizio_pubblicazione'])) $params ['data_inizio_pubblicazione'] = date('Y-m-d H:i:s');
        if (!isset($params ['data_inizio_pubblicazione']) || empty($params ['data_fine_pubblicazione'])) $params['data_fine_pubblicazione'] = null;

        $params ['data_creazione'] = date('Y-m-d H:i:s');
        $form = new BlocksForms\NewForm();
        $block = new Blocks();

        $block->assign($params);

        if (!$form->isValid($params, $block)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$block->save()) {
            $this->flash->error($block->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'edit/' . $$block->id
            ]);
        } else {
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function cloneAction($id)
    {
        $controller_data = Blocks::findFirstById($id);
        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $sparams = $controller_data->toArray();
        $this->getDI()->getSession()->set('sparams', $sparams);
        return $this->response->redirect($this->controllerName . '/new');
    }

    public function editAction($id)
    {

        $controller_data = Blocks::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new BlocksForms\EditForm($controller_data);

        /**
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();
            if (!isset($params ['data_inizio_pubblicazione']) || empty($params ['data_inizio_pubblicazione'])) $params ['data_inizio_pubblicazione'] = date('Y-m-d H:i:s');
            if (!isset($params ['data_inizio_pubblicazione']) || empty($params ['data_fine_pubblicazione'])) $params['data_fine_pubblicazione'] = null;

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
        $this->view->id = $id;
        $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
        $this->view->form = $form;
        $this->view->controller_data = $controller_data;
        $this->view->controller_data_history = $controller_data->getBlocksHistory(['order' => 'id DESC']);

        $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');

        $this->assets->addJs('assets/admin/js/codemirror/codemirror.js');
        $this->addLibraryAssets(['codemirror'], $this->controllerName . '-new');
        $this->assets->addJs('assets/admin/js/blocks/text-editor.js');
        $this->assets->addJs('assets/admin/js/blocks/edit.js');

    }

    public function saveAction()
    {
        /*
         * Verifica se la richiesa è in POST (richiesta update)
         */
        if ($this->request->isPost() && $this->request->isAjax()) {

            $params = $this->request->getPost();
            $controller_data = Blocks::findFirstById($params['id']);
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

    public function deleteAction($id)
    {

        $controller_data = Blocks::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->response->redirect($this->controllerName . '/index');
        }

        if (!$controller_data->delete()) {
            foreach ($controller_data->getMessages() as $message) {
                $this->flashSession->error($message);
                return $this->response->redirect($this->controllerName . '/index');
            }
        }

        $this->flashSession->success($this->alert_messagge['successDelete']);

        return $this->response->redirect($this->controllerName . '/index');

    }

    public function templatesAction()
    {
        $this->assets->addJs('assets/admin/js/codemirror/codemirror.js');
        $this->assets->addJs('assets/admin/js/blocks/templates.js');
        $this->addLibraryAssets(['codemirror'], $this->controllerName . '-new');
        $this->assets->addCss('assets/admin/css/blocks/style.css');
    }

    public function scanAction()
    {
        if ($this->request->isAjax()) {
            $response = $this->scan($this->config->application->siteViewsDir);
            $this->response->setJsonContent([
                "name"  => 'views',
                "type"  => "folder",
                "path"  => $this->config->application->siteViewsDir,
                "items" => $response
            ]);
        } else {
            $this->response->setJsonContent([]);
        }

        return $this->response;
    }

    private function scan($dir)
    {
        $files = [];

        // Is there actually such a folder/file?
        if (file_exists($dir)) {

            foreach (scandir($dir) as $f) {

                if (!$f || $f[0] == '.' || $f[0] == '.DS_Store') {
                    continue;
                }

                if (is_dir($dir . '/' . $f)) {
                    // The path is a folder
                    $files[] = [
                        "name"  => $f,
                        "type"  => "folder",
                        "path"  => $dir . '/' . $f,
                        "items" => $this->scan($dir . '/' . $f) // Recursively get the contents of the folder
                    ];
                } else {
                    $files[] = [
                        "name" => $f,
                        "type" => "file",
                        "path" => $dir . '/' . $f,
                        "size" => filesize($dir . '/' . $f) // Gets the size of this file
                    ];
                }
            }
        }
        return $files;
    }

    public function readTemplateAction()
    {
        if ($this->request->isPost() && $this->request->isAjax() && $this->request->hasPost('path')) {
            if (!file_exists($this->request->getPost('path'))) {
                $this->response->setJsonContent(['success' => false, 'content' => 'Il file non esiste!']);
            } else {
                $path = $this->request->getPost('path');
                $tpl = file_get_contents($path);
                $response = ['success' => true, 'content' => $tpl];

                $post_types = [];
                $tipologie_post = TipologiePost::find();

                foreach ($tipologie_post as $tp) {
                    $post_types[] = $tp->slug;
                }

                $strpath = strstr($path, 'views/');

                $parts = explode('/', $strpath);
                $postType = in_array($parts[1], $post_types) ? $parts[1] : null;

                if (!is_null($postType)) {
                    $response['vars'] = [];
                    $meta_values = Options::findFirst([
                        'conditions' => 'option_name = ?1 AND attivo = 1',
                        'bind'       => [1 => 'columns_map_' . $this->config->application->defaultCode . '_' . $postType . '_meta']
                    ]);
                    $response['vars']['meta'] = json_decode($meta_values->option_value, true);
                    $filter_values = Options::findFirst([
                        'conditions' => 'option_name = ?1 AND attivo = 1',
                        'bind'       => [1 => 'columns_map_' . $this->config->application->defaultCode . '_' . $postType . '_filter']
                    ]);
                    $response['vars']['filters'] = json_decode($filter_values->option_value, true);
                }

                $this->response->setJsonContent($response);
            }
        } else {
            $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
        }
        return $this->response;
    }

    public function editTemplateAction()
    {
        if ($this->request->isPost() && $this->request->isAjax() && $this->request->hasPost('file') && $this->request->hasPost('content')) {
            $file = $this->request->getPost('file');

            if (!file_exists($file['path'])) {
                $this->response->setJsonContent(['success' => false, 'content' => 'Il file non esiste!']);
            } else {
                file_put_contents($file['path'], $this->request->getPost('content'));
                $this->response->setJsonContent(['success' => true, 'content' => 'File modificato con successo']);
            }
        } else {
            $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
        }
        return $this->response;
    }

}
