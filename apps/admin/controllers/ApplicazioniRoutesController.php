<?php

use apps\admin\forms\applicazioni_routes as ApplicazioniRoutesForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class ApplicazioniRoutesController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Route Applicazione');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Route Applicazione non trovato!';

        $this->alert_messagge['successCreate'] = 'Route Applicazione creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione route applicazione!';

        $this->alert_messagge['successUpdate'] = 'Route Applicazione aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento route applicazione!';

        $this->alert_messagge['successDelete'] = 'Route Applicazione eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione route applicazione!';

        $this->jqGrid_columns = [
            ['label' => 'Stato route applicazione', 'name' => 'id_tipologia_stato'],
            ['label' => 'Tipo route applicazione', 'name' => 'id_tipologia_route']
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Route Applicazione', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new ApplicazioniRoutesForms\IndexForm();
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

                $query = self::fromInput($this->di, 'ApplicazioniRoutes', $search);
                $query->andWhere('ApplicazioniRoutes.attivo = 1');

                $query->innerJoin('TipologieStatoApplicazioneRoute', 'ts.id = ApplicazioniRoutes.id_tipologia_stato AND ts.attivo = 1', 'ts');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'ApplicazioniRoutes.' . $sort . ' ' . $order;
                $parameters['group'] = 'ApplicazioniRoutes.id';

                //effettua la ricerca
                $controller_data = ApplicazioniRoutes::find($parameters);

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
                    $item->id_tipologia_stato = $item->TipologieStatoApplicazioneRoute->descrizione;
                    $item->id_tipologia_route = $item->TipologieRoutes->descrizione;

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
        $this->view->form = new ApplicazioniRoutesForms\NewForm();
    }

    public function createAction()
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) {
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'index'
            ]);
        }

        $params = $this->request->getPost();
        $params['data_creazione'] = date('Y-m-d H:i:s');
        $form = new ApplicazioniRoutesForms\NewForm();
        $ApplicazioniRoutes = new ApplicazioniRoutes();
        $ApplicazioniRoutes->assign($params);

        if (!$form->isValid($params, $ApplicazioniRoutes)) {
            foreach ($form->getMessages() as $message) {
                $this->response->setJsonContent(['error' => $message->__toString()]);
                return $this->response;
            }
        }

        if (!$ApplicazioniRoutes->save()) {
            $this->response->setJsonContent(['error' => $this->alert_messagge['failCreate']]);
        } else {
            $routes = ApplicazioniRoutes::find(
                [
                    'conditions' => 'id_applicazione = ' . $params['id_applicazione']
                ]
            );

            //$view = clone $this->view;

            $rs = $this->view->getRender('applicazioni', 'applicazioniRoutesList', ['routes' => $routes], function ($view) {
                $view->setViewsDir("../apps/admin/views/partials/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
            $this->response->setJsonContent(['success' => $this->alert_messagge['successCreate'], 'data' => $rs]);
        }
        return $this->response;
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
    public function editAction($id)
    {
        if ($this->request->isAjax()) {
            $controller_data = ApplicazioniRoutes::findFirstById($id);

            if (!$controller_data) {
                return $this->response->setJsonContent(['error' => $this->alert_messagge['failEditForm']]);
            }

            $form = new ApplicazioniRoutesForms\EditForm($controller_data);

            /*
             * Verifica se la richiesa � in POST (richiesta update)
             */
            if ($this->request->isPost()) {

                $params = $this->request->getPost();

                if ($form->isValid($params)) {

                    $controller_data->assign($params);

                    if ($controller_data->save()) {

                        $routes = ApplicazioniRoutes::find([
                            'conditions' => 'id_applicazione = ' . $params['id_applicazione']
                        ]);

                        //$view = clone $this->view;

                        $rs = $this->view->getRender('applicazioni', 'applicazioniRoutesList', ['routes' => $routes], function ($view) {
                            $view->setViewsDir("../apps/admin/views/partials/");
                            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                        });
                        $this->response->setJsonContent(['success' => $this->alert_messagge['successUpdate'], 'data' => $rs]);

                    } else {
                        foreach ($controller_data->getMessages() as $message) {
                            $messages[] = $message;
                        }
                        $this->response->setJsonContent(['error' => $this->alert_messagge['failUpdate']]);
                    }

                } else {
                    $this->response->setJsonContent(['error' => $this->alert_messagge['failEditForm']]);
                }
                return $this->response;

            } else {
                $this->view->form = $form;

                $rs = $this->view->getRender('applicazioni', 'applicazioniRoutesEditForm', ['form_applicazione_route_edit' => $form], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });

                $this->response->setJsonContent(['data' => $rs]);
                return $this->response;
            }

            $this->view->id = $id;
            $this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
            $this->view->form = $form;
            $this->view->controller_data = $controller_data;


            $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');

            $this->assets->addJs('assets/admin/js/applicazioni_routes/edit.js');
        } else {
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function saveAction()
    {

    }

    public function deleteAction($id)
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $controller_data = ApplicazioniRoutes::findFirstById($id);

            if (!$controller_data) {
                $this->response->setJsonContent(['error' => $this->alert_messagge['failDelete']]);
            }

            if (!$controller_data->delete()) {
                $this->response->setJsonContent(['error' => $this->alert_messagge['failDelete']]);
            } else {
                $routes = ApplicazioniRoutes::find(
                    [
                        'conditions' => 'id_applicazione = ' . $params['id_applicazione']
                    ]
                );

                //$view = clone $this->view;

                $rs = $this->view->getRender('applicazioni', 'applicazioniRoutesList', ['routes' => $routes], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });
                $this->response->setJsonContent(['success' => $this->alert_messagge['successDelete'], 'data' => $rs]);
            }

            return $this->response->redirect($this->controllerName . '/index');
        } else {
            return $this->response->redirect($this->controllerName . '/index');
        }

    }

}
