<?php

use apps\admin\forms\meta_group as MetaGroupForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class MetaGroupController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Gruppo Meta');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Gruppo Meta non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Gruppo Meta creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione gruppo meta!';
		
		$this->alert_messagge['successUpdate'] = 'Gruppo Meta aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento gruppo meta!';
		
		$this->alert_messagge['successDelete'] = 'Gruppo Meta eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione gruppo meta!';
		
		$this->jqGrid_columns = array(
				array('label'=>'Descrizione', 'name'=>'descrizione', 'editable' => true,
						'type' => 'text',
						'editrules' => array (
							'required' => true 
						) 
				),
				array('label'=>'Priorita', 'name'=>'priorita', 'editable' => true,
						'type' => 'text',
						'editrules' => array (
							'required' => true 
						) 
				),
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Gruppo Meta', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new MetaGroupForms\IndexForm();
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
																
				$query = self::fromInput($this->di, 'MetaGroup', $search);
				$query->andWhere('MetaGroup.attivo = 1');
					
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'MetaGroup.' . $sort . ' ' . $order;
				$parameters['group'] = 'MetaGroup.id';
			
				//effettua la ricerca
				$controller_data = MetaGroup::find($parameters);
			
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
		$this->view->form = new MetaGroupForms\NewForm();
	}
	
	public function createAction(){
		if (! $this->request->isPost ()) {
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'index'
			) );
		}
		
		$params = $this->request->getPost ();
        $params = $this->request->getPost ();
        if(isset($params['meta_group_post_types'])){
            $post_types = $params['meta_group_post_types'];
            unset($params['meta_group_post_types  ']);
        }
		$params ['data_creazione'] = date ( 'Y-m-d H:i:s' );
		$form = new MetaGroupForms\NewForm();
		$meta = new MetaGroup();
		
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

		$transaction = $this->beginTransaction();
		if (! $meta->save ()) {
			$this->flash->error ( $meta->getMessages () );
            $transaction->rollback();
			return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
			) );
		} else {
            if(isset($post_types) && !empty($post_types)){
                foreach($post_types as $id_tipologia_post){
                    $ms = new MetaGroupPostType();
                    $insert = ['id_meta_group' => $meta->id, 'id_tipologia_post' => $id_tipologia_post, 'attivo' => 1];

                    if(!$ms->save($insert)){
                        $this->flash->error ( $ms->getMessages () );
                        return $this->dispatcher->forward ( array (
                            'controller' => $this->router->getControllerName(),
                            'action' => 'new',
                            'attivo' => 1
                        ) );
                        $transaction->rollback();
                    }
                }
            }
			$this->flashSession->success ( $this->alert_messagge ['successCreate'] );
            $transaction->commit();
			$form->clear ();
			return $this->response->redirect ( $this->controllerName . '/index' );
		}
	}

	public function editAction($id){
        $selected = MetaGroupPostType::find(['conditions' => 'id_meta_group = '.$id]);
		$controller_data = MetaGroup::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new MetaGroupForms\EditForm($controller_data);
				
		/**
		 * Verifica se la richiesa è in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();

            if(isset($params['meta_group_post_types'])){
                $post_types = $params['meta_group_post_types'];
                unset($params['meta_group_post_types']);
            }

			if($form->isValid($params)){
				$controller_data->assign($params);

                $transaction = $this->beginTransaction();
				if($controller_data->save()){
                    if(isset($post_types) && !empty($post_types)){
                        $existing_associations = [];
                        foreach($selected as $assoc){
                            if(in_array($assoc->id_tipologia_post, $post_types)){
                                $existing_associations[] = $assoc->id_tipologia_post;
                            } else {
                                $assoc->delete();
                            }
                        }

                        foreach($post_types as $id_tipologia_post){
                            if(in_array($id_tipologia_post, $existing_associations)){
                                continue;
                            }
                            $ms = new MetaGroupPostType();

                            $update = ['id_meta_group' => $controller_data->id, 'id_tipologia_post' => $id_tipologia_post, 'attivo' => 1];

                            if(!$ms->save($update)){
                                $this->flash->error ( $ms->getMessages () );
                                return $this->dispatcher->forward ( array (
                                    'controller' => $this->router->getControllerName(),
                                    'action' => 'new'
                                ) );
                                $transaction->rollback();
                            }
                        }
                    }
					$this->flashSession->success($this->alert_messagge['successUpdate']);
                    $transaction->commit();
					return $this->response->redirect($this->controllerName.'/index');
				}else{
					$message = array();
					foreach($controller_data->getMessages() as $message){
						$messages[] = $message;
					}
                    $transaction->rollback();
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

        $meta_group_selected_post_types = [];

        if(!empty($selected)){
            $selected = $selected->toArray();
            foreach($selected as $key => $val){
                $meta_group_selected_post_types[] = $val['id_tipologia_post'];
            }
        }
        $this->view->meta_group_selected_post_types = json_encode($meta_group_selected_post_types);
		
		$this->addLibraryAssets(array('jQueryValidation', 'dataTables'), $this->controllerName.'-edit');
		
		$this->assets->addJs('assets/admin/js/meta_group/edit.js');
		
	}
	
	public function saveAction(){
		
		/*
		 * Verifica se la richiesa � in POST (richiesta update)
		 */
		if($this->request->isPost() && $this->request->isAjax()){
		
			$params = $this->request->getPost();
			$controller_data = MetaGroup::findFirstById($params['id']);
			
			if(!$controller_data){
				$this->response->setJsonContent(array('error' => $this->alert_messagge['failUpdate']));
			}
			
			$form = new MetaGroupForms\EditForm($controller_data);
				
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
	
		$controller_data = MetaGroup::findFirstById($id);
		
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
