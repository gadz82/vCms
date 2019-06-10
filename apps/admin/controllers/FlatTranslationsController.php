<?php

use apps\admin\forms\flat_translations as FlatTranslationsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FlatTranslationsController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Traduzione Stringa');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Traduzione Stringa non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Traduzione Stringa creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione traduzione stringa!';
		
		$this->alert_messagge['successUpdate'] = 'Traduzione Stringa aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento traduzione stringa!';
		
		$this->alert_messagge['successDelete'] = 'Traduzione Stringa eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione traduzione stringa!';
		
		$this->jqGrid_columns = array(
				array('label'=>'Stringa', 'name'=>'original_string'),
				array('label'=>'Stringa', 'name'=>'translation'),
				array('label'=>'Codice App', 'name'=>'id_applicazione')
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Traduzione Stringa', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new FlatTranslationsForms\IndexForm();
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
																
				$query = self::fromInput($this->di, 'FlatTranslations', $search);
                $query->innerJoin('Applicazioni', 'a.id = FlatTranslations.id_applicazione And a.attivo = 1 AND a.id_tipologia_stato = 1', 'a');
				$query->andWhere('FlatTranslations.attivo = 1');

				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'FlatTranslations.' . $sort . ' ' . $order;
				$parameters['group'] = 'FlatTranslations.id';
			
				//effettua la ricerca
				$controller_data = FlatTranslations::find($parameters);
			
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
			
				$paging = $paginator->getPaginate();
                foreach($paging->items as $item){
                    $item->id_applicazione = $item->Applicazioni->titolo.' - <b>'.strtoupper($item->Applicazioni->codice).'</b>';
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
		$this->view->form = new FlatTranslationsForms\NewForm();
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
        $params['id_utente'] = $this->auth['id'];
        $form = new FlatTranslationsForms\NewForm();
        $FlatTranslations = new FlatTranslations();
        $FlatTranslations->assign ( $params );

        if (! $form->isValid ( $params, $FlatTranslations )) {
            foreach ( $form->getMessages () as $message ) {
                $this->flash->error ( $message );
            }
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        }

        if (!$FlatTranslations->save ()) {
            $this->flash->error ( $FlatTranslations->getMessages () );
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
		
		$controller_data = FlatTranslations::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new FlatTranslationsForms\EditForm($controller_data);
				
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
		
		$this->assets->addJs('assets/admin/js/flat_translations/edit.js');
		
	}
	
	public function saveAction(){
		
	}
	
	public function deleteAction($id){
	
		$controller_data = FlatTranslations::findFirstById($id);
		
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
