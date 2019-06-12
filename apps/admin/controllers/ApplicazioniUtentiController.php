<?php

use apps\admin\forms\applicazioni_utenti as ApplicazioniUtentiForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class ApplicazioniUtentiController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('ApplicazioniUtenti');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'ApplicazioniUtenti non trovato!';

        $this->alert_messagge['successCreate'] = 'ApplicazioniUtenti creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione applicazioniutenti!';

        $this->alert_messagge['successUpdate'] = 'ApplicazioniUtenti aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento applicazioniutenti!';

        $this->alert_messagge['successDelete'] = 'ApplicazioniUtenti eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione applicazioniutenti!';

        $this->jqGrid_columns = [
            ['label' => 'Nome Utente', 'name' => 'id_utente_applicazione'],
            ['label' => 'Applicazione', 'name' => 'id_applicazione'],
        ];
        $this->grid_actions = ['delete'];
    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'ApplicazioniUtenti', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new ApplicazioniUtentiForms\IndexForm();
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

                $query = self::fromInput($this->di, 'ApplicazioniUtenti', $search);
                $query->andWhere('ApplicazioniUtenti.attivo = 1');

                $query->innerJoin('Applicazioni', 'a.id = ApplicazioniUtenti.id_applicazione AND a.attivo = 1', 'a');
                $query->innerJoin('Utenti', 'u.id = ApplicazioniUtenti.id_utente_applicazione AND u.attivo = 1', 'u');


                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'ApplicazioniUtenti.' . $sort . ' ' . $order;
                $parameters['group'] = 'ApplicazioniUtenti.id';

                //effettua la ricerca
                $controller_data = ApplicazioniUtenti::find($parameters);

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
                foreach ($paging->items as $i) {
                    $i->id_utente_applicazione = $i->Utenti->nome . ' ' . $i->Utenti->cognome;
                    $i->id_applicazione = $i->Applicazioni->titolo;
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
        $this->view->form = new ApplicazioniUtentiForms\NewForm();
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
        $auth = $this->getDI()->getSession()->get('auth-identity');

        $form = new ApplicazioniUtentiForms\EditForm();
        $applicazione_utente = new ApplicazioniUtenti();

        $applicazione_utente->assign($params);

        if (!$form->isValid($params, $applicazione_utente)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }
        if (!$applicazione_utente->save()) {
            $this->flash->error($applicazione_utente->getMessages());
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

        $controller_data = ApplicazioniUtenti::findFirstById($id);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new ApplicazioniUtentiForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();

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

        $this->assets->addJs('js/applicazioni_utenti/edit.js');

    }

    public function saveAction()
    {

    }

    public function deleteAction($id)
    {

        $controller_data = ApplicazioniUtenti::findFirstById($id);

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
