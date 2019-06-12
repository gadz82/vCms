<?php

use apps\admin\forms\form_fields as FormFieldsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FormFieldsController extends ControllerBase
{

    public function initialize()
    {

        $this->tag->setTitle('Campo Form');
        parent::initialize();

        $this->alert_messagge['notFound'] = 'Campo Form non trovato!';

        $this->alert_messagge['successCreate'] = 'Campo Form creato con successo!';
        $this->alert_messagge['failCreate'] = 'Errore creazione campo form!';

        $this->alert_messagge['successUpdate'] = 'Campo Form aggiornato con successo!';
        $this->alert_messagge['failUpdate'] = 'Errore aggiornamento campo form!';

        $this->alert_messagge['successDelete'] = 'Campo Form eliminato con successo!';
        $this->alert_messagge['failDelete'] = 'Errore eliminazione campo form!';

        $this->jqGrid_columns = [
            ['label' => 'Stato campo form', 'name' => 'id_tipologia_stato'],
            ['label' => 'Tipo campo form', 'name' => 'id_tipologia_form_field']
        ];
    }

    public function indexAction()
    {

        parent::indexAction();

        $jqGrid_select_editoptions = [];

        $this->view->entityId = str_replace('/', '_', $this->controllerName);
        $this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Campo Form', $this->jqGrid_columns, $jqGrid_select_editoptions);

        $form = new FormFieldsForms\IndexForm();
        $this->view->form = $form;

        $this->assets->addJs('js/grid.js');

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

                $query = self::fromInput($this->di, 'FormFields', $search);
                $query->andWhere('FormFields.attivo = 1');

                $query->innerJoin('TipologieStatoFormFields', 'ts.id = FormFields.id_tipologia_stato AND ts.attivo = 1', 'ts');

                $this->persistent->parameters = $query->getParams();
                $this->persistent->searchParams = $search;

                $parameters = $this->persistent->parameters;
                if (!is_array($parameters)) $parameters = [];

                //verifica ordinamento
                $sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
                $order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

                $parameters ['order'] = 'FormFields.' . $sort . ' ' . $order;
                $parameters['group'] = 'FormFields.id';

                //effettua la ricerca
                $controller_data = FormFields::find($parameters);

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
                    $item->id_tipologia_stato = $item->TipologieStatoFormField->descrizione;
                    $item->id_tipologia_form_field = $item->TipologieFormField->descrizione;
                    $item->avanzamento = $avanzamento;
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

    }

    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
        }

        $params = $this->request->getPost();
        if ($this->request->isAjax() == true) {

            $form_fields = new FormFields();
            $params['data_creazione'] = date('Y-m-d H:i:s');
            if (!isset($params['placeholder']) || $params['placeholder'] == '') $params['placeholder'] = new \Phalcon\Db\RawValue("''");;
            $params['obbligatorio'] = isset($params['obbligatorio']) && $params['obbligatorio'] == 'on' ? '1' : '0';
            $form_fields->assign($params);

            $form = new FormFieldsForms\NewForm();
            if (!$form->isValid($params, $form_fields)) {
                foreach ($form->getMessages() as $message) {
                    $this->response->setJsonContent(['error' => $message->__toString()]);
                    return $this->response;
                }
            }

            if (!$form_fields->save()) {
                foreach ($form_fields->getMessages() as $message) {
                    $this->response->setJsonContent(['error' => $message->__toString()]);
                    return $this->response;
                }
            } else {

                $form_fields = FormFields::find(
                    [
                        'conditions' => 'id_form = ' . $params['id_form']
                    ]
                );

                //$view = clone $this->view;

                $rs = $this->view->getRender('form_fields', 'formFieldsList', ['form_fields' => $form_fields], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });

                $this->response->setJsonContent(['success' => $this->alert_messagge['successCreate'], 'data' => $rs]);
            }

            return $this->response;

        } else {
            return $this->response->redirect($this->controllerName . '/index');
        }
    }

    public function editAction($id)
    {
        if ($this->request->isAjax()) {

            $controller_data = FormFields::findFirst([
                'conditions' => 'id = ?1',
                'bind'       => [1 => $id]
            ]);

            if (!$controller_data) {
                $this->flashSession->error($this->alert_messagge['notFound']);
                return $this->dispatcher->forward(['controller' => $this->controllerName, 'action' => 'index']);
            }

            $form = new FormFieldsForms\EditForm($controller_data);

            /*
             * Verifica se la richiesa � in POST (richiesta update)
             */
            if ($this->request->isPost()) {

                $params = $this->request->getPost();

                $params['obbligatorio'] = isset($params['obbligatorio']) && $params['obbligatorio'] == 'on' ? '1' : '0';
                if (!isset($params['placeholder']) || $params['placeholder'] == '') $params['placeholder'] = new \Phalcon\Db\RawValue("''");;
                if ($form->isValid($params)) {
                    $controller_data->assign($params, $controller_data->columnMap());
                    if ($controller_data->save()) {
                        $form_fields = FormFields::find(
                            [
                                'conditions' => 'id_form = ' . $params['id_form']
                            ]
                        );

                        $rs = $this->view->getRender('form_fields', 'formFieldsList', ['form_fields' => $form_fields], function ($view) {
                            $view->setViewsDir("../apps/admin/views/partials/");
                            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                        });

                        $this->response->setJsonContent(['success' => $this->alert_messagge['successUpdate'], 'data' => $rs]);
                    } else {
                        $messages = [];
                        foreach ($controller_data->getMessages() as $message) {
                            $messages[] = $message;
                        }

                        $this->response->setJsonContent(['success' => $this->alert_messagge['failUpdate'], 'data' => implode(' | ', $messages)]);
                    }

                } else {
                    PhalconDebug::debug('qui');
                    $message = [];
                    foreach ($form->getMessages() as $message) {
                        $messages[] = $message;
                    }
                    $this->response->setJsonContent(['success' => $this->alert_messagge['failUpdate'], 'data' => implode(' | ', $messages)]);
                }

            } else {

                $this->view->form = $form;

                $rs = $this->view->getRender('form_fields', 'formFieldsEditForm', ['form_form_fields_edit' => $form, 'id_form_field' => $controller_data->id], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });

                $this->response->setJsonContent(['data' => $rs]);
            }
            return $this->response;
        }

    }

    public function saveAction()
    {

    }

    public function deleteAction($id)
    {
        $controller_data = FormFields::findFirstById($id);
        //print_r($controller_data->toArray());exit();
        if ($this->request->isAjax()) {
            $this->view->disable();
            if (!$controller_data) {
                $this->response->setJsonContent(['success' => false]);
                return $this->response;
            }
            if (!$controller_data->delete()) {
                return $this->response->setJsonContent(['success' => false]);
            } else {
                $form_fields = FormFields::find(
                    [
                        'conditions' => 'id_form = ' . $controller_data->id_form
                    ]
                );

                $rs = $this->view->getRender('form_fields', 'formFieldsList', ['form_fields' => $form_fields], function ($view) {
                    $view->setViewsDir("../apps/admin/views/partials/");
                    $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
                });

                $this->response->setJsonContent(['success' => $this->alert_messagge['successUpdate'], 'data' => $rs]);
                return $this->response;
            }
        } else {
            return $this->response->redirect($this->controllerName . '/index');
        }

    }

}
