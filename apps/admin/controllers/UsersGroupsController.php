<?php

use apps\admin\forms\users_groups as UsersGroupsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class UsersGroupsController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Gruppo Users');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Gruppo Users non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Gruppo Users creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione gruppo users!';
		
		$this->alert_messagge['successUpdate'] = 'Gruppo Users aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento gruppo users!';
		
		$this->alert_messagge['successDelete'] = 'Gruppo Users eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione gruppo users!';
		
		$this->jqGrid_columns = array(
				array('label'=>'Stato gruppo users', 'name'=>'id_tipologia_stato'),
				array('label'=>'Tipo gruppo users', 'name'=>'id_tipologia_user_group')
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Gruppo Users', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new UsersGroupsForms\IndexForm();
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
																
				$query = self::fromInput($this->di, 'UsersGroups', $search);
				$query->andWhere('UsersGroups.attivo = 1');
								
				$query->innerJoin('TipologieStatoUserGroup', 'ts.id = UsersGroups.id_tipologia_stato AND ts.attivo = 1', 'ts');
			
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'UsersGroups.' . $sort . ' ' . $order;
				$parameters['group'] = 'UsersGroups.id';
			
				//effettua la ricerca
				$controller_data = UsersGroups::find($parameters);
			
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
				foreach($paging->items as $item){	
					$item->id_tipologia_stato = $item->TipologieStatoUserGroup->descrizione;
					$item->id_tipologia_user_group = $item->TipologieUserGroup->descrizione;
					$item->avanzamento = $avanzamento;
					
				}
				
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
		$this->view->form = new UsersGroupsForms\NewForm();
	}
	
	public function createAction(){
	    if (! $this->request->isPost ()) {
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'index'
            ) );
        }

        $params = $this->request->getPost ();
        $params['data_creazione'] = date ( 'Y-m-d H:i:s' );
        $form = new UsersGroupsForms\NewForm();
        $UsersGroups = new Notifiche();
        $UsersGroups->assign ( $params );

        if (! $form->isValid ( $params, $UsersGroups )) {
            foreach ( $form->getMessages () as $message ) {
                $this->flash->error ( $message );
            }
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        }

        if (!$UsersGroups->save ()) {
            $this->flash->error ( $UsersGroups->getMessages () );
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
		
		$controller_data = UsersGroups::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new UsersGroupsForms\EditForm($controller_data);
				
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
		
		$this->assets->addJs('assets/admin/js/users_groups/edit.js');
		
	}
	
	public function saveAction(){
		
	}
	
	public function deleteAction($id){
	
		$controller_data = UsersGroups::findFirstById($id);
		
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
