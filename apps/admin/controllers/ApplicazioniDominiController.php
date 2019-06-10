<?php

use apps\admin\forms\applicazioni_domini as ApplicazioniDominiForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class ApplicazioniDominiController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('ApplicazioniDomini');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'ApplicazioniDomini non trovato!';
		
		$this->alert_messagge['successCreate'] = 'ApplicazioniDomini creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione Dominio associato ad Applicazione!';
		$this->alert_messagge['successEdit'] = 'ApplicazioniDomini modificato con successo!';
		$this->alert_messagge['failEdit'] = 'Errore modifica Dominio associato ad Applicazione!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione Dominio associato ad Applicazione!';
		$this->alert_messagge['failEditForm'] = 'Errore Form Dominio associato ad Applicazione!';
		
		$this->alert_messagge['successUpdate'] = 'ApplicazioniDomini aggiornato con successo!';
		$this->alert_messagge['successDelete'] = 'ApplicazioniDomini eliminato!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento applicazionidomini!';
		
		$this->alert_messagge['failDelete'] = 'Errore eliminazione applicazionidomini!';
		
		$this->jqGrid_columns = array(
				array('label'=>'Stato applicazionidomini', 'name'=>'id_tipologia_stato'),
				array('label'=>'Tipo applicazionidomini', 'name'=>'id_tipologia_applicazioni_domini')
		);
		
	}
	
	public function indexAction(){
						
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'ApplicazioniDomini', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new ApplicazioniDominiForms\IndexForm();
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
																
				$query = self::fromInput($this->di, 'ApplicazioniDomini', $search);
				$query->andWhere('ApplicazioniDomini.attivo = 1');
								
				$query->innerJoin('TipologieStatoApplicazioniDomini', 'ts.id = ApplicazioniDomini.id_tipologia_stato AND ts.attivo = 1', 'ts');
			
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'ApplicazioniDomini.' . $sort . ' ' . $order;
				$parameters['group'] = 'ApplicazioniDomini.id';
			
				//effettua la ricerca
				$controller_data = ApplicazioniDomini::find($parameters);
			
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
					$item->id_tipologia_stato = $item->TipologieStatoApplicazioniDomini->descrizione;
					$item->id_tipologia_applicazioni_domini = $item->TipologieApplicazioniDomini->descrizione;
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
		
	}
	
	public function createAction(){

		if(!$this->request->isPost()){
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
		
		$params = $this->request->getPost();
		
		$auth = $this->session->get('auth-identity');
		
				
		if($this->request->isAjax() == true){
							
			$applicazione_dominio = new ApplicazioniDomini();
			$applicazione_dominio->id_applicazione = $params['id_applicazione'];
			$applicazione_dominio->referer = $params['referer'];
			$applicazione_dominio->ip_autorizzati = !empty($params['ip_autorizzati']) ? $params['ip_autorizzati'] : null;
			$applicazione_dominio->data_creazione = date('Y-m-d H:i:s');
				
			$form = new ApplicazioniDominiForms\NewForm();
			if(!$form->isValid($params,$applicazione_dominio)){
				foreach($form->getMessages() as $message){
					$this->response->setJsonContent(array('error' => $message->__toString()));
					return $this->response;
				}
			}
				
			if(!$applicazione_dominio->save()){
				$this->response->setJsonContent(array('error' => $this->alert_messagge['failCreate']));
			}else{
		
				$domini = ApplicazioniDomini::find(
					[
						'conditions' => 'id_applicazione = '.$params['id_applicazione']
					]
				);
		
				//$view = clone $this->view;
					
				$rs = $this->view->getRender('applicazioni', 'applicazioniDominiList', array('domini' => $domini), function ($view){
					$view->setViewsDir("../apps/admin/views/partials/");
					$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
				});
		
				$this->response->setJsonContent(array('success' => $this->alert_messagge['successCreate'], 'data' => $rs));
			}
				
			return $this->response;
				
		}else{
			return $this->response->redirect($this->controllerName.'/index');
		}
	}

    /**
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|void
     */
	public function editAction($id){
		if($this->request->isAjax()){

			$controller_data = ApplicazioniDomini::findFirst($id);
			
			if(!$controller_data){
				$this->flashSession->error($this->alert_messagge['notFound']);
				return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
			}
					
			$form = new ApplicazioniDominiForms\EditForm($controller_data);
					
			/*
			 * Verifica se la richiesa è in POST (richiesta update)
			 */
			if($this->request->isPost()){
					
				$params = $this->request->getPost();
				if($form->isValid($params)){

					$controller_data->assign($params);


					if($controller_data->save()){
						$domini = ApplicazioniDomini::find(
								[
										'conditions' => 'id_applicazione = '.$params['id_applicazione']
								]
                        );
						
						//$view = clone $this->view;
							
						$rs = $this->view->getRender('applicazioni', 'applicazioniDominiList', array('domini' => $domini), function ($view){
							$view->setViewsDir("../apps/admin/views/partials/");
							$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
						});
						
						$this->response->setJsonContent(array('success' => $this->alert_messagge['successEdit'], 'data' => $rs));
					}else{
                        foreach($controller_data->getMessages() as $message){
                            $messages[] = $message;
                        }
						$this->response->setJsonContent(array('error' => $this->alert_messagge['failEdit']));
					}

				}else{

					$this->response->setJsonContent(array('error' => $this->alert_messagge['failEditForm']));
				}
				return $this->response;

			} else {

				$this->view->form = $form;
				
				$rs = $this->view->getRender('applicazioni', 'applicazioniDominiEditForm', array('form_applicazione_dominio_edit' => $form), function ($view){
					$view->setViewsDir("../apps/admin/views/partials/");
					$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
				});
				
				$this->response->setJsonContent(array('data' => $rs));
				return $this->response;
			}
		} else {
			return $this->response->redirect($this->controllerName.'/index');
		}
	}
	
	public function saveAction(){
		
	}
	
	public function deleteAction($id){
		if($this->request->isAjax() && $this->request->isPost()){
			$params = $this->request->getPost();
			$controller_data = ApplicazioniDomini::findFirstById($id);
			
			if(!$controller_data){
				$this->response->setJsonContent(array('error' => $this->alert_messagge['failDelete']));
			}

			if(!$controller_data->delete()){
				$this->response->setJsonContent(array('error' => $this->alert_messagge['failDelete']));
			} else {
				$domini = ApplicazioniDomini::find(
						[
							'conditions' => 'id_applicazione = '.$params['id_applicazione']
						]
					);
				
				$rs = $this->view->getRender('applicazioni', 'applicazioniDominiList', array('domini' => $domini), function ($view){
					$view->setViewsDir("../apps/admin/views/partials/");
					$view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
				});
				
				$this->response->setJsonContent(array('success' => $this->alert_messagge['successDelete'], 'data' => $rs));
			}
			
			return $this->response;
		} else {
			return $this->response->redirect($this->controllerName.'/index');
		}
		
	}
		
}
