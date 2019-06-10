<?php

use apps\admin\forms\meta as MetaForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class MetaController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Meta');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Meta non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Meta creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione meta!';
		
		$this->alert_messagge['successUpdate'] = 'Meta aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento meta!';
		
		$this->alert_messagge['successDelete'] = 'Meta eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione meta!';
		
		$this->jqGrid_columns = array(
				array('label'=>'Nome Meta', 'name'=>'key', 'editable' => true,
						'type' => 'text',
						'editrules' => array (
							'required' => true 
						) 
				),
				array('label'=>'Label', 'name'=>'label', 'editable' => true,
						'type' => 'text',
						'editrules' => array (
							'required' => true 
						) 
				),
				array('label'=>'Gruppo Meta', 'name'=>'id_meta_group', 'editable' => true,
						'type' => 'select',
						'editrules' => array (
							'required' => true 
						) ),
				array('label'=>'Tipo meta', 'name'=>'id_tipologia_meta', 'editable' => true,
						'type' => 'select',
						'editrules' => array (
							'required' => true 
						) ),
				array('label'=>'Priorita', 'name'=>'priorita', 'editable' => true,
						'type' => 'text',
						'editrules' => array (
							'required' => true 
						) ),
				array('label'=>'Obbligatorio', 'name'=>'required', 'editable' => false)
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();

		$jqGrid_select_editoptions = array (
            'id_meta_group' => 'MetaGroup',
            'id_tipologia_meta' => 'TipologieMeta'
		);
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Meta', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new MetaForms\IndexForm();
		$this->view->form = $form;
		
		$this->assets->addJs('assets/admin/js/grid.js');
		
	}
	
	public function searchAction(){
						
		if($this->request->isPost() || $this->request->hasPost('export')){
			if($this->request->isAjax() || $this->request->hasPost('export')){
					
				if($this->request->hasPost('form_search')){
					$data = $this->request->getPost('form_search');
					parse_str($data,$search);
				} else {
					$search = $this->request->getPost();
				}
																
				$query = self::fromInput($this->di, 'Meta', $search);
				$query->andWhere('Meta.attivo = 1');
				$query->innerJoin('MetaGroup', 'mg.id = Meta.id_meta_group AND mg.attivo = 1', 'mg');
			
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'Meta.' . $sort . ' ' . $order;
				$parameters['group'] = 'Meta.id';
			
				//effettua la ricerca
				$controller_data = Meta::find($parameters);
			
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
					$item->id_tipologia_meta = $item->TipologieMeta->descrizione;
					$item->id_meta_group = $item->MetaGroup->descrizione;
					$item->required = $item->required == '1' ? 'Si' : 'No';
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
		$this->view->form = new MetaForms\NewForm();
	}
	
	public function createAction(){
		if (! $this->request->isPost ()) {
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'index'
			) );
		}
		
		$params = $this->request->getPost ();
		$params['required'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
        $params['hidden'] = isset($params['hidden']) && $params['hidden'] == 'on' ? '1' : '0';
        $params['key'] = self::slugify($params['key']);
		$params ['data_creazione'] = date ( 'Y-m-d H:i:s' );
		$form = new MetaForms\NewForm();
		$meta = new Meta();
		
		$meta->assign ( $params );
		
		if (! $form->isValid ( $params, $meta )) {
			foreach ( $form->getMessages () as $message ) {
				$this->flash->error ( $message );
			}
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'new'
			) );
		}
		
		if (! $meta->save ()) {
			$this->flash->error ( $meta->getMessages () );
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
		
		$controller_data = Meta::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new MetaForms\EditForm($controller_data);
				
		/*
		 * Verifica se la richiesa � in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();
			$params['required'] = isset($params['required']) && $params['required'] == 'on' ? '1' : '0';
            $params['hidden'] = isset($params['hidden']) && $params['hidden'] == 'on' ? '1' : '0';
            $params['key'] = self::slugify($params['key']);

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
		
		$this->assets->addJs('js/meta/edit.js');
		
	}
	
	public function saveAction(){
		/*
		 * Verifica se la richiesa è in POST (richiesta update)
		 */
		if($this->request->isPost() && $this->request->isAjax()){
		
			$params = $this->request->getPost();
            $params['key'] = self::slugify($params['key']);
			$controller_data = Meta::findFirstById($params['id']);
				
			if(!$controller_data){
				$this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
			}
				
			$form = new MetaForms\EditForm($controller_data);
		
            $controller_data->assign($params);

            if($controller_data->save()){
                $this->response->setJsonContent(array('success' => $this->alert_messagge['successUpdate']));
            }else{
                $this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
            }

		} else {
			$this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
		}
		return $this->response;
	}
	
	public function deleteAction($id){
	
		$controller_data = Meta::findFirstById($id);
		
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
