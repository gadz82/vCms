<?php

namespace apps\admin\forms\applicazioni;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Applicazioni\Applicazioni;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente_admin', 'descrizione');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Applicazioni(), 'Applicazioni', $exclude_fields, $order_fields, true);
	
		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato applicazione');
		$fields['id_tipologia_applicazione']->setLabel('Tipologia applicazione');

				
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}