<?php
use apps\admin\forms\utenti as UtentiForms;

use Phalcon\Paginator\Adapter\Model as Paginator;
class UtentiController extends ControllerBase {
	public function initialize() {
		
		// Set the document title
		$this->tag->setTitle ( 'Utenti' );
		parent::initialize ();
		
		$this->alert_messagge ['notFound'] = 'Utente non trovato';
		
		$this->alert_messagge ['successCreate'] = 'Utente creato con successo!';
		$this->alert_messagge ['failCreate'] = 'Errore creazione utente!';
		
		$this->alert_messagge ['successUpdate'] = 'Utente aggiornato con successo!';
		$this->alert_messagge ['failUpdate'] = 'Errore aggiornamento utente!';
		
		$this->alert_messagge ['successDelete'] = 'Utente eliminato con successo!';
		$this->alert_messagge ['failDelete'] = 'Errore eliminazione utente!';
		
		$this->jqGrid_columns = array (
				array (
						'label' => 'Nome utente',
						'name' => 'nome_utente' 
				),
				array (
						'label' => 'Nome',
						'name' => 'nome',
						'editable' => true,
						'type' => 'text',
						'editrules' => array (
								'required' => true 
						) 
				),
				array (
						'label' => 'Cognome',
						'name' => 'cognome',
						'editable' => true,
						'type' => 'text',
						'editrules' => array (
								'required' => true 
						) 
				),
				array (
						'label' => 'Tipologia',
						'name' => 'id_tipologia_utente',
						'editable' => true,
						'type' => 'select',
						'editrules' => array (
								'required' => true 
						) 
				),
				array (
						'label' => 'Stato',
						'name' => 'id_tipologia_stato',
						'editable' => true,
						'type' => 'select',
						'editrules' => array (
								'required' => true 
						) 
				),
				array (
						'label' => 'Ruolo',
						'name' => 'id_ruolo',
						'editable' => true,
						'type' => 'select',
						'editrules' => array (
								'required' => true 
						) 
				),
				array (
						'label' => 'Data creazione',
						'name' => 'data_creazione' 
				) 
		);
	}
	
	/**
	 * Index action
	 */
	public function indexAction() {
		parent::indexAction ();
		
		$jqGrid_select_editoptions = array (
				'id_tipologia_utente' => 'TipologieUtente',
				'id_tipologia_stato' => 'TipologieStatoUtente',
				'id_ruolo' => 'Ruoli' 
		);
		
		$this->view->entityId = str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init ( $this->controllerName, 'Utenti', $this->jqGrid_columns, $jqGrid_select_editoptions );
		
		$form = new UtentiForms\IndexForm ();
		$this->view->form = $form;
		
		$this->assets->addJs ( 'assets/admin/js/grid.js' );
	}
	
	/**
	 * Searches for ordini
	 */
	public function searchAction() {
		if ($this->request->isPost () || $this->request->hasPost ( 'export' )) {
			if ($this->request->isAjax () || $this->request->hasPost ( 'export' )) {
				
				if ($this->request->hasPost ( 'form_search' )) {
					$data = $this->request->getPost ( 'form_search' );
					parse_str ( $data, $search );
				} else {
					$search = $this->request->getPost ();
				}
				
				$query = self::fromInput ( $this->di, 'Utenti', $search );
				$query->andWhere ( 'Utenti.attivo = 1' );
				
				$this->persistent->parameters = $query->getParams ();
				$this->persistent->searchParams = $search;
				
				$parameters = $this->persistent->parameters;
				if (! is_array ( $parameters ))
					$parameters = array ();
					
					// verifica ordinamento
				$sort = ($this->request->hasPost ( 'sort' ) && ! empty ( $this->request->getPost ( 'sort' ) )) ? $this->request->getPost ( 'sort' ) : 'id';
				$order = ($this->request->hasPost ( 'order' ) && ! empty ( $this->request->getPost ( 'order' ) )) ? $this->request->getPost ( 'order' ) : 'DESC';
				
				$parameters ['order'] = 'Utenti.' . $sort . ' ' . $order;
				$parameters ['group'] = 'Utenti.id';
				
				// effettua la ricerca
				$utenti = Utenti::find ( $parameters );
				
				if ($utenti->count () == 0)
					return $this->response;
					
					// crea l'oggetto paginator
				if ($this->request->hasPost ( 'export' )) {
					$paginator = new Paginator ( array (
							'data' => $utenti,
							'limit' => 65000,
							'page' => 1 
					) );
				} else {
					$paginator = new Paginator ( array (
							'data' => $utenti,
							'limit' => ($this->request->hasPost ( 'rows' ) && ! empty ( $this->request->getPost ( 'rows' ) )) ? $this->request->getPost ( 'rows' ) : 20,
							'page' => ($this->request->hasPost ( 'page' ) && ! empty ( $this->request->getPost ( 'page' ) )) ? $this->request->getPost ( 'page' ) : 1 
					) );
				}
				
				$paging = $paginator->getPaginate ();
				foreach ( $paging->items as $utente ) {
					$utente->id_ruolo = $utente->Ruoli->descrizione;
					$utente->id_tipologia_utente = $utente->TipologieUtente->descrizione;
					$utente->id_tipologia_stato = $utente->TipologieStatoUtente->descrizione;
				}
				
				if ($this->request->hasPost ( 'export' )) {
					// crea un file excel con il risultato della ricerca
					$this->jqGridExport ( $paging->items );
				} else {
					// crea l'array grid da passare a jqgrid
					$grid = array (
							'records' => $paging->total_items,
							'page' => $paging->current,
							'total' => $paging->total_pages,
							'rows' => $paging->items 
					);
					
					$this->response->setJsonContent ( $grid );
					return $this->response;
				}
			}
		}
	}
	
	/**
	 * Displays the creation form
	 */
	public function newAction() {
		$this->view->form = new UtentiForms\NewForm ();
	}
	
	/**
	 * Edits a ordini
	 *
	 * @param string $id        	
	 */
	public function editAction($id) {
		$utente = Utenti::findFirstById ( $id );
		
		if (! $utente) {
			$this->flash->error ( $this->alert_messagge ['notFound'] );
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'index' 
			) );
		}
		
		$this->view->id = $id;
		
		$form = new UtentiForms\EditForm ( $utente );
		$this->view->form = $form;
	}
	
	/**
	 * Creates a new ordini
	 */
	public function createAction() {
		if (! $this->request->isPost ()) {
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'index' 
			) );
		}
		
		$params = $this->request->getPost ();
		$params ['password'] = (isset ( $params ['password'] )) ? $this->security->hash ( $params ['password'] ) : '';
		
		$params ['data_creazione'] = date ( 'Y-m-d H:i:s' );
		
		$avatar_list = array ();
		$file = array_diff ( scandir ( 'img/avatar' ), array (
				'.',
				'..' 
		) );
		foreach ( $file as $f ) {
			if (in_array ( strtolower ( pathinfo ( $f, PATHINFO_EXTENSION ) ), array (
					'png' 
			), true ))
				$avatar_list [] = 'avatar/' . $f;
		}
		$params ['avatar'] = $avatar_list [array_rand ( $avatar_list )];
		
		$form = new UtentiForms\EditForm ();
		$utente = new Utenti ();
		
		$utente->assign ( $params );
		
		if (! $form->isValid ( $params, $utente )) {
			foreach ( $form->getMessages () as $message ) {
				$this->flash->error ( $message );
			}
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'new' 
			) );
		}
		
		if (! $utente->save ()) {
			$this->flash->error ( $utente->getMessages () );
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
	
	/**
	 * Saves a ordini edited
	 */
	public function saveAction() {
		if (! $this->request->isPost ()) {
			return $this->dispatcher->forward ( array (
					'controller' => $this->router->getControllerName(),
					'action' => 'index' 
			) );
		}
		
		$id = $this->request->getPost ( 'id', 'int' );
		$utente = Utenti::findFirstById ( $id );
		
		$attributes = $utente->getModelsMetaData ()->getAttributes ( $utente );
		$denyFieldsUpdate = array (
				'token',
				'data_creazione_token',
				'data_creazione',
				'attivo' 
		);
		
		$params = $this->request->getPost ();
		$params ['password'] = (isset ( $params ['password'] ) && $params ['password'] != $utente->password) ? $this->security->hash ( $params ['password'] ) : $utente->password;
		
		if ($this->request->isAjax () == true) {
			
			if (! $utente) {
				$this->response->setJsonContent ( array (
						'error' => $this->alert_messagge ['notFound'] 
				) );
				return $this->response;
			}
			
			if (! $utente->update ( $params, array_diff ( $attributes, $denyFieldsUpdate ) )) {
				$this->response->setJsonContent ( array (
						'error' => $this->alert_messagge ['failUpdate'] 
				) );
			} else {
				$this->response->setJsonContent ( array (
						'success' => $this->alert_messagge ['successUpdate'] 
				) );
			}
			
			return $this->response;
		} else {
			
			if (! $utente) {
				$this->flash->error ( $this->alert_messagge ['notFound'] );
				return $this->dispatcher->forward ( array (
						'controller' => $this->router->getControllerName(),
						'action' => 'index' 
				) );
			}
			
			$form = new UtentiForms\EditForm ( $utente );
			if (! $form->isValid ( $params, $utente )) {
				foreach ( $form->getMessages () as $message ) {
					$this->flash->error ( $message );
				}
				return $this->dispatcher->forward ( array (
						'action' => 'edit',
						'params' => array (
								$utente->id 
						) 
				) );
			}

			if (! $utente->update ( $params, array_diff ( $attributes, $denyFieldsUpdate ) )) {
				foreach ( $utente->getMessages () as $message ) {
					$this->flash->error ( $message );
				}
				return $this->dispatcher->forward ( array (
						'action' => 'edit',
						'params' => array (
								$utente->id 
						) 
				) );
			}
			$this->flashSession->success ( $this->alert_messagge ['successUpdate'] );
			
			return $this->response->redirect ( $this->controllerName . '/index' );
		}
	}
	
	/**
	 * Deletes a ordini
	 *
	 * @param string $id        	
	 */
	public function deleteAction($id) {
		$utente = Utenti::findFirstById ( $id );
		
		if (! $utente) {
			$this->flashSession->error ( $this->alert_messagge ['notFound'] );
			return $this->response->redirect ( $this->controllerName . '/index' );
		}
		
		if (! $utente->delete ()) {
			foreach ( $utente->getMessages () as $message ) {
				$this->flashSession->error ( $message );
				return $this->response->redirect ( $this->controllerName . '/index' );
			}
		}
		
		$this->flashSession->success ( $this->alert_messagge ['successDelete'] );
		
		return $this->response->redirect ( $this->controllerName . '/index' );
	}
	public function getUtentiFromGruppiUtentiAction() {
		$id = $this->request->getPost ( 'id', 'int' );
		
		$utenti = \Utenti::query ()->columns ( 'Utenti.id, Utenti.nome_utente' )->innerJoin ( 'GruppiUtenti', 'gu.id_utente = Utenti.id AND gu.id_gruppo = :id_gruppo: AND gu.attivo = 1', 'gu' )->where ( 'Utenti.attivo = 1' )->bind ( array (
				'id_gruppo' => $id 
		) )->groupBy ( 'Utenti.id' )->order ( 'Utenti.nome_utente ASC' )->execute ();
		
		$data = array ();
		foreach ( $utenti as $u ) {
			$data [] = array (
					'id' => $u->id,
					'descrizione' => $u->nome_utente 
			);
		}
		
		echo json_encode ( $data );
	}
}
