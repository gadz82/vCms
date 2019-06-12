<?php

use apps\admin\forms\forms as FormsForms;
use apps\admin\forms\form_fields as FormFields;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FormsController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Form');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Form non trovato!';

        $this->alert_messagge['successCreate'] = 'Form creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione form!';

        $this->alert_messagge['successUpdate'] = 'Form aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento form!';

        $this->alert_messagge['successDelete'] = 'Form eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione form!';

        $this->jqGrid_columns = [
            ['label' => 'Stato form', 'name' => 'id_tipologia_stato'],
            ['label' => 'Nome form', 'name' => 'titolo'],
            ['label' => 'Tipo form', 'name' => 'id_tipologia_form'],
            ['label' => 'Key', 'name' => 'key']
        ];

    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Form', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new FormsForms\IndexForm();
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

                $query = self::fromInput($this->di, 'Forms', $search);
                $query->andWhere('Forms.attivo = 1');

                $query->innerJoin('TipologieStatoForm', 'ts.id = Forms.id_tipologia_stato AND ts.attivo = 1', 'ts');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'Forms.' . $sort . ' ' . $order;
                $parameters['group'] = 'Forms.id';

                //effettua la ricerca
                $controller_data = Forms::find($parameters);

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
                    $item->id_tipologia_stato = $item->TipologieStatoForm->descrizione;
                    $item->id_tipologia_form = $item->TipologieForm->descrizione;

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
        $this->view->form = new FormsForms\NewForm();
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
        $params['invio_utente'] = isset($params['invio_utente']) && $params['invio_utente'] == 'on' ? '1' : '0';
        $form = new FormsForms\NewForm();
        $f = new Forms();

        $f->assign($params);

        if (!$form->isValid($params, $f)) {
            foreach ($form->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        }

        if (!$f->save()) {
            $this->flash->error($f->getMessages());
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'new'
            ]);
        } else {
            $this->flashSession->success($this->alert_messagge ['successCreate']);
            $form->clear();
            return $this->response->redirect($this->controllerName . '/edit/' . $f->id);
        }
    }

    public function editAction($id)
    {

        $controller_data = Forms::findFirstById($id);
        $form_fields = \FormFields::find([
            'conditions' => "id_form = ?1 AND attivo = 1",
            "bind"       => [
                1 => $id
            ],
            'orderby'    => 'ordine ASC'
        ]);
        if (!$controller_data) {
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $form = new FormsForms\EditForm($controller_data);

        /*
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if ($this->request->isPost()) {

            $params = $this->request->getPost();
            $params['invio_utente'] = isset($params['invio_utente']) && $params['invio_utente'] == 'on' ? '1' : '0';

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

        $form_form_fields = new FormFields\NewForm();
        $this->view->form_form_fields_new = $form_form_fields;
        $this->view->form_fields = $form_fields;

        $this->addLibraryAssets(['jQueryValidation', 'dataTables'], $this->controllerName . '-edit');

        $this->assets->addJs('assets/admin/js/forms/edit.js');

    }

    public function saveAction()
    {

    }

    public function deleteAction($id)
    {

        $controller_data = Forms::findFirstById($id);

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
