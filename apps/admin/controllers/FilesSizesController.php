<?php

use apps\admin\forms\files_sizes as FilesSizesForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FilesSizesController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('File Size');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'File Size non trovato!';
		
		$this->alert_messagge['successCreate'] = 'File Size creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione file size!';
		
		$this->alert_messagge['successUpdate'] = 'File Size aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento file size!';
		
		$this->alert_messagge['successDelete'] = 'File Size eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione file size!';
		
		$this->jqGrid_columns = array(
            array('label'=>'Key Size', 'name'=>'key'),
            array('label'=>'Larghezza', 'name'=>'max_width'),
            array('label'=>'Altezza', 'name'=>'max_height'),
            array('label'=>'Crop', 'name'=>'crop')
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'File Size', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new FilesSizesForms\IndexForm();
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
																
				$query = self::fromInput($this->di, 'FilesSizes', $search);
				$query->andWhere('FilesSizes.attivo = 1');

				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'FilesSizes.' . $sort . ' ' . $order;
				$parameters['group'] = 'FilesSizes.id';
			
				//effettua la ricerca
				$controller_data = FilesSizes::find($parameters);
			
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
                    $item->crop = $item->crop == '0' ? 'No' : 'Si';
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
		$this->view->form = new FilesSizesForms\NewForm();
	}
	
	public function createAction(){
        if (! $this->request->isPost ()) {
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'index'
            ) );
        }

        $params = $this->request->getPost ();
        $form = new FilesSizesForms\NewForm();
        $fileSizes = new FilesSizes();

        $fileSizes->assign ( $params );

        if (! $form->isValid ( $params, $fileSizes )) {
            foreach ( $form->getMessages () as $message ) {
                $this->flash->error ( $message );
            }
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        }

        if (! $fileSizes->save ()) {
            $this->flash->error ( $fileSizes->getMessages () );
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new'
            ) );
        } else {
            apcu_clear_cache();
            $this->flashSession->success ( $this->alert_messagge ['successCreate'] );
            $form->clear ();
            if(!file_exists(FILES_DIR.$fileSizes->key)){
                mkdir(FILES_DIR.$fileSizes->key, 0755);
            }
            return $this->response->redirect ( $this->controllerName . '/index' );
        }
	}

	public function editAction($id){
		
		$controller_data = FilesSizes::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new FilesSizesForms\EditForm($controller_data);
				
		/*
		 * Verifica se la richiesa � in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();
			
			if($form->isValid($params)){
		
				$controller_data->assign($params);
		
				if($controller_data->save()){
				    apcu_clear_cache();
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
		
		$this->assets->addJs('js/files_sizes/edit.js');
		
	}
	
	public function saveAction(){
		
	}
	
	public function deleteAction($id){
	
		$controller_data = FilesSizes::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->response->redirect($this->controllerName.'/index');
		}
        $key = $controller_data->key;
        if($controller_data->delete()){

            $dir = FILES_DIR.$key;
            array_map('unlink', glob("$dir/*.*"));
            rmdir($dir);

        } else {
			foreach($controller_data->getMessages() as $message){
				$this->flashSession->error($message);
				return $this->response->redirect($this->controllerName.'/index');
			}
		}
	
		$this->flashSession->success($this->alert_messagge['successDelete']);
	
		return $this->response->redirect($this->controllerName.'/index');
		
	}
		
}
