<?php

use apps\admin\forms\ruoli_menu as RuoliMenuForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class RuoliMenuController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Ruolo Menu Admin');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Ruolo Menu Admin non trovato!';

        $this->alert_messagge['successCreate'] = 'Ruolo Menu Admin creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione ruolo menu admin!';

        $this->alert_messagge['successUpdate'] = 'Ruolo Menu Admin aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento ruolo menu admin!';

        $this->alert_messagge['successDelete'] = 'Ruolo Menu Admin eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione ruolo menu admin!';

        $this->jqGrid_columns = [
            [
                'label' => 'Id',
                'name'  => 'id'
            ],
            [
                'label'     => 'Ruolo',
                'name'      => 'id_ruolo',
                'editable'  => true,
                'type'      => 'select',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Livello',
                'name'      => 'livello',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Risorsa',
                'name'      => 'risorsa',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => false
                ]
            ],
            [
                'label'     => 'Azione',
                'name'      => 'azione',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => false
                ]
            ],
            [
                'label'     => 'Descrizione',
                'name'      => 'descrizione',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Id Menu padre',
                'name'      => 'id_padre',
                'editable'  => true,
                'type'      => 'text',
                'editrules' => [
                    'required' => true
                ]
            ],
            [
                'label'     => 'Ord',
                'name'      => 'ordine',
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
            'id_ruolo' => 'Ruoli'
        ];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Ruolo Menu Admin', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new RuoliMenuForms\IndexForm();
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

                $query = self::fromInput($this->di, 'RuoliMenu', $search);
                $query->andWhere('RuoliMenu.attivo = 1');

                $query->innerJoin('Ruoli', 'r.id = RuoliMenu.id_ruolo AND r.attivo = 1', 'r');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'RuoliMenu.' . $sort . ' ' . $order;
                $parameters['group'] = 'RuoliMenu.id';
                \PhalconDebug::debug($search);
                //effettua la ricerca
                $controller_data = RuoliMenu::find($parameters);

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
                    $item->id_ruolo = $item->Ruoli->descrizione;
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
        $this->view->form = new RuoliMenuForms\NewForm();
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
        $params ['visible'] = '1';
        $params ['header'] = '0';
        $params ['id_padre'] = !isset($params ['id_padre']) || empty($params ['id_padre']) ? '0' : $params ['id_padre'];
        $form = new RuoliMenuForms\NewForm();
        $ruoliMenu = new RuoliMenu();

        $ruoliMenu->assign($params);

        if (!$form->isValid($params, $ruoliMenu)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$ruoliMenu->save()) {
            $this->flash->error($ruoliMenu->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {
            apcu_clear_cache();
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function editAction($id)
    {

        $controller_data = RuoliMenu::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new RuoliMenuForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();
            $params ['id_padre'] = !isset($params ['id_padre']) || empty($params ['id_padre']) ? '0' : $params ['id_padre'];
            $params ['visible'] = '1';
            $params ['header'] = '0';
            if ($form->isValid($params)) {

                $controller_data->assign($params);

                if ($controller_data->save()) {
                    apcu_clear_cache();
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

        $this->view->id = $id;
        $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
        $this->view->form = $form;
        $this->view->controller_data = $controller_data;


        $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');

        $this->assets->addJs('assets/admin/js/ruoli_menu/edit.js');

    }

    public function saveAction()
    {
        /**
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost() && $this->request->isAjax()) {

            $params = $this->request->getPost();
            $params ['visible'] = '1';
            $params ['header'] = '0';
            $controller_data = RuoliMenu::findFirstById($params['id']);

            if (!$controller_data) {
                $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
            }
            $params['class'] = $controller_data->class;
            $form = new RuoliMenuForms\EditForm($controller_data);

            if ($form->isValid($params)) {

                $controller_data->assign($params);
                if ($controller_data->save()) {
                    apcu_clear_cache();
                    $this->response->setJsonContent(['success' => $this->alert_messagge['successUpdate']]);
                } else {
                    $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
                }

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

        $controller_data = RuoliMenu::findFirstById($id);

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

}
