<?php

namespace apps\admin\forms\applicazioni_utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Select;

class NewForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');
		
		$exclude_fields = array('id','data_cambio_stato','data_creazione','data_aggiornamento','attivo');
		$order_fields = array();
		
		$this->fields = $this->getAutoRenderByModel(new \ApplicazioniUtenti(), 'ApplicazioniUtenti', $exclude_fields, $order_fields, false);
		$select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni: \Applicazioni::find(array('conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'descrizione ASC'));
		$id_app = new Select('id_applicazione', $select_app, array('class'=>'form-control selectpicker', 'using' => array('id','descrizione'), 'data-style' => 'btn-flat btn-white', 'data-size'=>5, 'data-width'=>'100%', 'data-live-search'=>true, 'data-selected-text-format'=>'count>1', 'useEmpty'=>false, 'emptyText'=>'---'));
		$id_app->setLabel('Applicazione');
		$this->fields['id_applicazione'] = $id_app;
		
		$select_utente = isset($this->view->Utenti) ? $this->view->Utenti: \Utenti::find(array('conditions'=>'attivo = 1','columns'=>'id,CONCAT(nome," ", cognome ) AS descrizione','order'=>'id ASC'));
		$id_utente = new Select('id_utente_applicazione', $select_utente, array('class'=>'form-control selectpicker', 'using' => array('id','descrizione'), 'data-style' => 'btn-flat btn-white', 'data-size'=>5, 'data-width'=>'100%', 'data-live-search'=>true, 'data-selected-text-format'=>'count>1', 'useEmpty'=>false, 'emptyText'=>'---'));
		$id_utente->setLabel('Utente');
		$this->fields['id_utente_applicazione'] = $id_utente;
		
		$this->fields = $this->reorderFields($this->fields, $order_fields);
		
		/* PREPARE VALIDATION */
		$stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
		$this->prepareValidation($stato);
		/* FINE BLOCCO */
		
		/* ASSEGNAZIONE FIELDS->FORM */
		foreach($this->fields as $name=>$field){
			$this->add($field);
		}
		
		if(isset($entity)){
			$this->add(new Hidden('id', array('hidden'=>true, 'value' => $entity->id)));
		}
		/* FINE BLOCCO */
		
	}
	
	private function prepareValidation($id_tipologia_stato){

		$arr_exclude_required = array();

		$this->custom_validation = array();

		$arr_exclude_required['1'] = array('nota','nota_new');
		$arr_exclude_required['default'] = array('nota','nota_new');

		$this->compileValidation($id_tipologia_stato, $arr_exclude_required);

	}
	
	private function compileValidation($id_tipologia_stato, $arr_exclude_required){

		$exclude_required = false;

		foreach($arr_exclude_required as $key=>$val){
			if(in_array($id_tipologia_stato, explode('|',$key))){
				$exclude_required = $val;
				break;
			}
		}

		$this->exclude_required = !$exclude_required ? $arr_exclude_required['default'] : $exclude_required;

		$arr_render_required = array();
		$render = array_keys($this->fields);

		foreach($arr_exclude_required as $key=>$val){
			$render_required = array_diff($render,$val);
			sort($render_required);

			$arr_render_required[$key] = $render_required;
		}

		$this->getDi()->getView()->render_required = json_encode($arr_render_required);

	}
	
	public function beforeValidation(){

		$params = $this->request->getPost();
		$id_tipologia_stato = $params['id_tipologia_stato'];

		$this->prepareValidation($id_tipologia_stato);

		$this->fields = $this->addValidateControl($this->fields, $this->custom_validation, $this->exclude_required);

	}
	
}
