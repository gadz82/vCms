<?php

use apps\admin\forms\filtri as FiltriForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FiltriController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Filtro');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Filtro non trovato!';

        $this->alert_messagge['successCreate'] = 'Filtro creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione filtro!';

        $this->alert_messagge['successUpdate'] = 'Filtro aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento filtro!';

        $this->alert_messagge['successDelete'] = 'Filtro eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione filtro!';

        $this->jqGrid_columns = [
            ['label' => 'Applicazione', 'name' => 'id_applicazione'],
            ['label' => 'Nome', 'name' => 'titolo'],
            ['label' => 'Stato filtro', 'name' => 'id_tipologia_stato'],
            ['label' => 'Gruppo Filtri', 'name' => 'id_filtri_group'],
            ['label' => 'Obbligatorio', 'name' => 'required'],
            ['label' => 'Tipo filtro', 'name' => 'id_tipologia_filtro']
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Filtro', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new FiltriForms\IndexForm();
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

                $query = self::fromInput($this->di, 'Filtri', $search);
                $query->andWhere('Filtri.attivo = 1');

                $query->innerJoin('TipologieStatoFiltro', 'ts.id = Filtri.id_tipologia_stato AND ts.attivo = 1', 'ts');
                $query->innerJoin('FiltriGroup', 'fg.id = Filtri.id_filtri_group AND fg.attivo = 1', 'fg');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'Filtri.' . $sort . ' ' . $order;
                $parameters['group'] = 'Filtri.id';

                //effettua la ricerca
                $controller_data = Filtri::find($parameters);

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
                    $item->id_applicazione = $item->Applicazioni->descrizione;
                    $item->id_tipologia_stato = $item->TipologieStatoFiltro->descrizione;
                    $item->id_filtri_group = $item->FiltriGroup->descrizione;
                    $item->id_tipologia_filtro = $item->TipologieFiltro->descrizione;
                    $item->required = $item->required == '1' ? 'Si' : 'No';
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
        $this->view->form = new FiltriForms\NewForm();
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
        $params['required'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
        $params['one_to_one'] = isset($params['one_to_one']) && $params['one_to_one'] == 'on' ? '1' : '0';
        $params['frontend_filter'] = isset($params['frontend_filter']) && $params['frontend_filter'] == 'on' ? '1' : '0';
        $params['key'] = self::slugify($params['key']);
        $params ['data_creazione'] = date('Y-m-d H:i:s');
        $form = new FiltriForms\NewForm();
        $filtri = new Filtri();
        $filtri->assign($params);

        if (!$form->isValid($params, $filtri)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$filtri->save()) {
            $this->flash->error($filtri->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function editAction($id)
    {

        $controller_data = Filtri::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new FiltriForms\EditForm($controller_data);

        /**
         * Verifica se la richiesa è in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();
            $params['required'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
            $params['one_to_one'] = isset($params['one_to_one']) && $params['one_to_one'] == 'on' ? '1' : '0';
            $params['frontend_filter'] = isset($params['frontend_filter']) && $params['frontend_filter'] == 'on' ? '1' : '0';
            $params['key'] = self::slugify($params['key']);
            if ($form->isValid($params)) {

                $controller_data->assign($params);

                if ($controller_data->save()) {
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

        $this->assets->addJs('assets/admin/js/filtri/edit.js');

    }

    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'index'
            ]);
        }

        $params = $this->request->getPost();
        $params['required'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
        $params['one_to_one'] = isset($params['one_to_one']) && $params['one_to_one'] == 'on' ? '1' : '0';
        $params['frontend_filter'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
        $params ['data_creazione'] = date('Y-m-d H:i:s');
        $params['key'] = self::slugify($params['key']);
        $form = new FiltriForms\EditForm();
        $filtri = new Filtri();

        $filtri->assign($params);

        if (!$form->isValid($params, $filtri)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$filtri->save()) {
            $this->flash->error($meta->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function deleteAction($id)
    {

        $controller_data = Filtri::findFirstById($id);

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
