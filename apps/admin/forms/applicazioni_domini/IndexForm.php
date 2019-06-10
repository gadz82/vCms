<?php

namespace apps\admin\forms\applicazioni_domini;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \ApplicazioniDomini(), 'ApplicazioniDomini', $exclude_fields, $order_fields, true);
	
		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato applicazionidomini');
		$fields['id_tipologia_applicazioni_domini']->setLabel('Tipologia applicazionidomini');

				
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}