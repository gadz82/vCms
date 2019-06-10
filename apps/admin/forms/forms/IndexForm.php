<?php

namespace apps\admin\forms\forms;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		$auth = $this->getAuth();

		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'testo', 'email_to', 'email_bcc', 'email_cc', 'data_aggiornamento');

		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();

		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Forms(), 'Forms', $exclude_fields, $order_fields, true);

		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato form');
		$fields['id_tipologia_form']->setLabel('Tipologia form');

        $select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni: \Applicazioni::find(array('conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'descrizione ASC'));
        $id_app = new Select(
            'id_applicazione',
            $select_app,
            [
                'class'=>'form-control selectpicker', 'using' => ['id','descrizione'],
                'data-style' => 'btn-flat btn-white',
                'data-size'=>5,
                'data-width'=>'100%',
                'data-live-search'=>true,
                'data-selected-text-format'=>'count>1',
                'useEmpty'=>true,
                'emptyText'=>'---'
            ]
        );

        $id_app->setLabel('Applicazione')->setAttribute('position', 'side');
        $fields['id_applicazione'] = $id_app;

		$fields = $this->reorderFields($fields, $order_fields);

		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}