<?php

use apps\admin\forms\applicazioni as ApplicazioniForms;

use Phalcon\Paginator\Adapter\Model as Paginator;
use apps\admin\forms\applicazioni_domini as ApplicazioniDominiForms;
use apps\admin\forms\applicazioni_routes as ApplicazioniRoutesForms;

class ApplicazioniController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Applicazione');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Applicazione non trovato!';

        $this->alert_messagge['successCreate'] = 'Applicazione creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione applicazione!';

        $this->alert_messagge['successUpdate'] = 'Applicazione aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento applicazione!';

        $this->alert_messagge['successDelete'] = 'Applicazione eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione applicazione!';

        $this->jqGrid_columns = [
            ['label' => 'Codice App', 'name' => 'codice'],
            ['label' => 'Titolo', 'name' => 'titolo'],
            ['label' => 'Stato applicazione', 'name' => 'id_tipologia_stato'],
            ['label' => 'Tipo applicazione', 'name' => 'id_tipologia_applicazione']
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];
        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Applicazione', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new ApplicazioniForms\IndexForm();
        $this->view->form = $form;
        $this->assets->addJs('assets/admin/js/grid.js');

    }

    public function searchAction()
    {
        if ($this->request->isPost() || $this->request->isAjax() || $this->request->hasPost('export')) {
            if ($this->request->hasPost('form_search')) {
                $data = $this->request->getPost('form_search');
                parse_str($data, $search);
            } else {
                $search = $this->request->getPost();
            }

            $query = self::fromInput($this->di, 'Applicazioni', $search);

            $query->andWhere('Applicazioni.attivo = 1 AND Applicazioni.id != 0');
            $query->innerJoin('TipologieStatoApplicazione', 'ts.id = Applicazioni.id_tipologia_stato AND ts.attivo = 1', 'ts');

            $this->persistent->parameters = $query->getParams();
            $this->persistent->searchParams = $search;

            $parameters = $this->persistent->parameters;
            if (!is_array($parameters)) $parameters = [];

            //verifica ordinamento
            $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
            $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

            $parameters ['order'] = 'Applicazioni.' . $sort . ' ' . $order;
            $parameters['group'] = 'Applicazioni.id';

            //effettua la ricerca
            $controller_data = Applicazioni::find($parameters);
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

            $paging = $paginator->getPaginate();
            foreach ($paging->items as $item) {
                $item->id_tipologia_stato = $item->TipologieStatoApplicazione->descrizione;
                $item->id_tipologia_applicazione = $item->TipologieApplicazione->descrizione;
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
        return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);

    }

    public function newAction()
    {
        $this->view->form = new ApplicazioniForms\NewForm();
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
        $params['id_utente_admin'] = $this->auth['id'];

        $form = new ApplicazioniForms\EditForm();
        $applicazione = new Applicazioni();

        $applicazione->assign($params);

        if (!$form->isValid($params, $applicazione)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$applicazione->save()) {
            $this->flash->error($applicazione->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {

            $this->createEmptyAppContents($applicazione->id);
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    private function createEmptyAppContents($id_applicazione)
    {
        $applicazioneDomini = new ApplicazioniDomini();
        $applicazioneDomini->id_applicazione = $id_applicazione;
        $applicazioneDomini->referer = '';
        $applicazioneDomini->data_creazione = date('Y-m-d H:i:s');
        $applicazioneDomini->save();
    }

    public function editAction($id)
    {

        $controller_data = Applicazioni::findFirstById($id);

        $domini = ApplicazioniDomini::find([
            'conditions' => "id_applicazione = ?1",
            "bind"       => [1 => $id]
        ]);

        $routes = ApplicazioniRoutes::find([
            'conditions' => "id_applicazione = ?1",
            "bind"       => [1 => $id]
        ]);

        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new ApplicazioniForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa è in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();

            if ($form->isValid($params)) {

                $controller_data->assign($params);

                if ($controller_data->save()) {
                    $this->flashSession->success($this->alert_messagge['successUpdate']);
                    return $this->response->redirect($this->controllerName . '/index');
                } else {
                    $messages = [];
                    foreach ($controller_data->getMessages() as $message) {
                        $messages[] = $message;
                    }
                    $this->flash->error(implode(' | ', $messages));
                }

            } else {
                $messages = [];
                foreach ($form->getMessages() as $message) {
                    $messages[] = $message;
                }
                $this->flash->error(implode(' | ', $messages));
            }

        }

        $this->view->id = $id;
        $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
        $this->view->form = $form;

        $form_applicazioni_domini = new ApplicazioniDominiForms\NewForm();
        $this->view->form_applicazione_dominio_new = $form_applicazioni_domini;
        $this->view->domini = $domini;

        $form_applicazioni_routes = new ApplicazioniRoutesForms\NewForm();
        $this->view->form_applicazione_route_new = $form_applicazioni_routes;
        $this->view->routes = $routes;

        $this->view->controller_data = $controller_data;

        $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');
        $this->assets->addJs('assets/admin/js/applicazioni/edit.js');
    }

    public function saveAction()
    {
        /**
         * Request Ajax
         */
    }

    public function deleteAction($id)
    {

        $controller_data = Applicazioni::findFirstById($id);

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
        $controller_data->triggerDelete();
        $this->flashSession->success($this->alert_messagge['successDelete']);
        return $this->response->redirect($this->controllerName . '/index');

    }

}
