<?php

use apps\admin\forms\form_requests as FormRequestsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FormRequestsController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Richiesta Form');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Richiesta Form non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Richiesta Form creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione richiesta form!';
		
		$this->alert_messagge['successUpdate'] = 'Richiesta Form aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento richiesta form!';
		
		$this->alert_messagge['successDelete'] = 'Richiesta Form eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione richiesta form!';
		
		$this->jqGrid_columns = array(
            array('label'=>'Form', 'name'=>'id_form'),
            array('label'=>'Contenuto Collegato', 'name'=>'id_post'),
            array('label'=>'Letto', 'name'=>'letto'),
            array('label'=>'Email', 'name'=>'email'),
            array('label'=>'Data / Ora', 'name'=>'data_creazione')
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Richiesta Form', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new FormRequestsForms\IndexForm();
		$this->view->form = $form;
		
		$this->assets->addJs('assets/admin/js/grid.js');
		
	}
	
	public function searchAction(){
						
		if($this->request->isPost() || $this->request->hasPost('export')){
			if($this->request->isAjax() || $this->request->hasPost('export')){
					
				if($this->request->hasPost('form_search')) {
					$data = $this->request->getPost('form_search');
					parse_str($data,$search);
				} else {
					$search = $this->request->getPost();
				}
				$query = self::fromInput($this->di, 'FormRequests', $search);

                if(isset($search['letto']) && $search['letto'] == 0){
                    $query->where('FormRequests.letto = 0');
                }

                if(isset($search['email']) && !empty($search['email'])){
                    $query->where('frf.input_value LIKE "%'.$search['email'].'%"');
                }

				$query->andWhere('FormRequests.attivo = 1');
				$query->leftJoin('Posts', 'p.id = FormRequests.id_post', 'p');
                $query->innerJoin('Forms', 'f.id = FormRequests.id_form AND f.attivo = 1', 'f');
                $query->leftJoin('FormFields', 'ff.id_form = f.id AND ff.id_tipologia_form_fields = 2 AND ff.id_tipologia_stato = 1 AND ff.attivo = 1', 'ff');
                $query->leftJoin('FormRequestsFields', 'frf.id_form_request = FormRequests.id AND frf.id_form = f.id AND frf.id_form_field = ff.id AND frf.attivo = 1', 'frf');
                if(isset($search['id_form']) && !empty($search['id_form'])){
                   $query->andWhere('f.id IN('.implode(',',$search['id_form']).') ');
                }
                if(isset($search['id_post']) && !empty($search['id_post'])){
                    $query->andWhere('p.id IN('.implode(',',$search['id_post']).') ');
                }
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'FormRequests.' . $sort . ' ' . $order;
			
				//effettua la ricerca
                $parameters['columns'] = '
                    FormRequests.id, 
                    p.titolo as id_post, 
                    f.titolo as id_form, 
                    FormRequests.letto, 
                    FormRequests.data_creazione, 
                    FormRequests.data_aggiornamento, 
                    FormRequests.attivo,
                    IFNULL(frf.input_value, "") AS email';

				$controller_data = FormRequests::find($parameters);
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
                    $item->letto = $item->letto == 1 ? 'Si' : 'No';
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
		
	}
	
	public function createAction(){
	
	}

	public function editAction($id){
		
		$controller_data = FormRequests::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new FormRequestsForms\EditForm($controller_data);
				
		/**
		 * Verifica se la richiesa è in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();
            $params['letto'] = isset($params['letto']) && $params['letto'] == 'on' ? '1' : '0';
			
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
		
		$this->view->post = $controller_data->Posts;

        $this->view->fields = FormRequestsFields::find([
           'conditions' => 'id_form_request = ?1 AND id_form = ?2',
           'bind' => [
                1 => $controller_data->id,
                2 => $controller_data->id_form
           ]
        ]);
		
		$this->addLibraryAssets(array('jQueryValidation', 'dataTables'), $this->controllerName.'-edit');
		
		$this->assets->addJs('js/form_requests/edit.js');
		
	}
	
	public function saveAction(){
		
	}
	
	public function deleteAction($id){
	
		$controller_data = FormRequests::findFirstById($id);
		
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
