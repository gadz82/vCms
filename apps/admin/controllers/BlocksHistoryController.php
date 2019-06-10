<?php

class BlocksHistoryController extends ControllerBase {
		
	public function initialize(){
				
		//Set the document title
		$this->tag->setTitle('Block History');
		parent::initialize();
		
	}
	
	/**
	* Index action
	*/
	public function indexAction(){
						
		parent::indexAction();
		
	}
	
	/**
	* Searches for ordini
	*/
	public function searchAction(){
			
		if($this->request->isPost()){
			
			if($this->request->isAjax() == true){
					
				$id = $this->request->getPost('id','int');
				$controller_data_history = BlocksHistory::findFirstById($id);
						
				if(!$controller_data_history){
					$this->response->setJsonContent(array('error' => 'History non trovata'));
					return $this->response;
				}
						
				$rs = $this->view->getRender('blocks', 'BlocksHistoryModal', array('blocks_history' => $controller_data_history), function ($view){
					$view->setViewsDir("../apps/admin/views/partials/");
					$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
				});
						
				$this->response->setJsonContent(array('success' => 'success', 'data' => $rs));
				return $this->response;
		
			}
		}
		
		$this->response->setJsonContent(array('error' => 'error'));
		return $this->response;
		
	}
	
	/**
	* Displays the creation form
	*/
	public function newAction(){
		
	}
	
	/**
	* Edits a ordini
	*
	* @param string $id
	*/
	public function editAction($id){
				
	}
	
	/**
	* Creates a new ordini
	*/
	public function createAction(){
		
	}
	
	/**
	* Saves a ordini edited
	*
	*/
	public function saveAction(){	
	}
	
	/**
	* Deletes a ordini
	*
	* @param string $id
	*/
	public function deleteAction($id){
	
	}
	
}
