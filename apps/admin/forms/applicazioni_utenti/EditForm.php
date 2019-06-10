<?php

namespace apps\admin\forms\applicazioni_utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;

class EditForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');
		
		$exclude_fields = array('id','attivo');
		$order_fields = array();
		
		$this->fields = $this->getAutoRenderByModel(new \ApplicazioniUtenti(), 'ApplicazioniUtenti', $exclude_fields, $order_fields, false);
		
		$this->fields = $this->reorderFields($this->fields, $order_fields);
		
		
		/* ASSEGNAZIONE FIELDS->FORM */
		foreach($this->fields as $name=>$field){
			$this->add($field);
		}
		
		if(isset($entity)){
			$this->add(new Hidden('id', array('hidden'=>true, 'value' => $entity->id)));
		}
		/* FINE BLOCCO */
		
	}
	
	
}
