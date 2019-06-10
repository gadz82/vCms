<?php

use apps\admin\forms\options as OptionsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class OptionsController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Option');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Option non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Option creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione option!';
		
		$this->alert_messagge['successUpdate'] = 'Option aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento option!';
		
		$this->alert_messagge['successDelete'] = 'Option eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione option!';
		
		$this->jqGrid_columns = [
				[
				    'label'=>'Nome Opzione',
                    'name'=>'option_name',
                    'editable' => true,
                    'type' => 'text',
                    'editrules' => [
                        'required' => true
                    ]
                ],
				[
				    'label'=>'Valore Opzione',
                    'name'=>'option_value',
                    'editable' => true,
                    'type' => 'text',
                    'editrules' => [
                        'required' => true
                    ]
                ]
		];
	}
	
	public function indexAction(){
						
		parent::indexAction();

		$jqGrid_select_editoptions = array();

		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Option', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new OptionsForms\IndexForm();
		$this->view->form = $form;
		
		$this->assets->addJs('assets/admin/js/grid.js');
		
	}
	
	public function searchAction(){
						
		if($this->request->isPost() || $this->request->hasPost('export')){
			if($this->request->isAjax() || $this->request->hasPost('export')){
					
				if($this->request->hasPost('form_search')){
					$data = $this->request->getPost('form_search');
					parse_str($data,$search);
				}else{
					$search = $this->request->getPost();
				}
																
				$query = self::fromInput($this->di, 'Options', $search);
				$query->andWhere('Options.attivo = 1');

				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'Options.' . $sort . ' ' . $order;
				$parameters['group'] = 'Options.id';
			
				//effettua la ricerca
				$controller_data = Options::find($parameters);
			
				if($controller_data->count() == 0) return $this->response;
									
				//crea l'oggetto paginator
				if($this->request->hasPost('export')){
					$paginator = new Paginator(array('data'=>$controller_data, 'limit'=>65000, 'page'=>1));
				}else{
					$paginator = new Paginator(array(
						'data'	=> $controller_data,
						'limit'	=> ($this->request->hasPost('rows') && !empty($this->request->getPost('rows'))) ? $this->request->getPost('rows') : 20,
						'page'	=> ($this->request->hasPost('page') && !empty($this->request->getPost('page'))) ? $this->request->getPost('page') : 1,
					));
				}
			
				$now = new DateTime(date('Y-m-d'));
				$paging = $paginator->getPaginate();

				
				if($this->request->hasPost('export')){
					//crea un file excel con il risultato della ricerca
					$this->jqGridExport($paging->items);
				}else{
					//crea l'array grid da passare a jqgrid
					$grid = array('records' => $paging->total_items,'page' => $paging->current,'total' => $paging->total_pages,'rows' => $paging->items);
						
					$this->response->setJsonContent($grid);
					return $this->response;
				}
			}
		}
		
		return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		
	}
	
	public function newAction(){
		$this->view->form = new OptionsForms\NewForm();
	}
	
	public function createAction(){
        if (! $this->request->isPost ()) {
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'index'
            ) );
        }

        $params = $this->request->getPost ();
        $params ['data_creazione'] = date ( 'Y-m-d H:i:s' );
        $form = new OptionsForms\NewForm();
        $option = new Options();

        $option->assign ( $params );

        if (! $form->isValid ( $params, $option )) {
            foreach ( $form->getMessages () as $message ) {
                $this->flash->error ( $message );
            }
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        }

        if (! $option->save ()) {
            $this->flash->error ( $option->getMessages () );
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        } else {
            $this->flashSession->success ( $this->alert_messagge ['successCreate'] );
            $form->clear ();
            return $this->response->redirect ( $this->controllerName . '/index' );
        }
	}

	public function editAction($id){
		
		$controller_data = Options::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new OptionsForms\EditForm($controller_data);
				
		/*
		 * Verifica se la richiesa � in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();
			
			if($form->isValid($params)){
		
				$controller_data->assign($params);
		
				if($controller_data->save()){
					$this->flashSession->success($this->alert_messagge['successUpdate']);
					return $this->response->redirect($this->controllerName.'/index');
				}else{
					$message = array();
					foreach($controller_data->getMessages() as $message){
						$messages[] = $message;
					}
					$this->flash->error(implode(' | ',$messages));
				}

			}else{
				$message = array();
				foreach($form->getMessages() as $message){
					$messages[] = $message;
				}
				$this->flash->error(implode(' | ',$messages));
			}
				
		}
				
		$this->view->id = $id;
		$this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
		$this->view->form = $form;
		$this->view->controller_data = $controller_data;
		
		
		
		$this->addLibraryAssets(array('jQueryValidation', 'dataTables'), $this->controllerName.'-edit');
		
		$this->assets->addJs('assets/admin/js/options/edit.js');
		
	}
	
	public function saveAction(){
        /*
         * Verifica se la richiesa � in POST (richiesta update)
         */
        if($this->request->isPost() && $this->request->isAjax()){

            $params = $this->request->getPost();
            $controller_data = Options::findFirstById($params['id']);

            if(!$controller_data){
                $this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
            }
            $form = new OptionsForms\EditForm($controller_data);

            if($form->isValid($params)){

                $controller_data->assign($params);

                if($controller_data->save()){
                    $this->response->setJsonContent(array('success' => $this->alert_messagge['successUpdate']));
                }else{
                    $this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
                }

            }else{
                $this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
            }
        } else {
            $this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
        }
        return $this->response;
	}
	
	public function deleteAction($id){
	
		$controller_data = Options::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->response->redirect($this->controllerName.'/index');
		}
		
		if(!$controller_data->delete()){
			foreach($controller_data->getMessages() as $message){
				$this->flashSession->error($message);
				return $this->response->redirect($this->controllerName.'/index');
			}
		}
	
		$this->flashSession->success($this->alert_messagge['successDelete']);
	
		return $this->response->redirect($this->controllerName.'/index');
		
	}
		
}
