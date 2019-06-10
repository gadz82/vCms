<?php

namespace apps\admin\forms\filtri_valori;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'key', 'meta_descrizione');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \FiltriValori(), 'FiltriValori', $exclude_fields, $order_fields, true);

        $select_gruppo_filtri = isset($this->view->Filtri) ? $this->view->Filtri: \Filtri::find(
            ['conditions'=>'attivo = 1', 'columns'=>'id,titolo', 'order'=>'id ASC']
        );
        $id_filtro = new Select('id_filtro[]', $select_gruppo_filtri,
            [
                'class'=>'form-control selectpicker',
                'using' => array('id','titolo'),
                'data-style' => 'btn-flat btn-white',
                'multiple'=>'multiple',
                'data-size'=>5,
                'data-width'=>'100%',
                'data-live-search'=>true,
                'data-selected-text-format'=>'count>1',
                'useEmpty'=>false,
                'emptyText'=>'---'
            ]
        );
        $id_filtro->setLabel('Filtro');
        $fields['id_filtri_group'] = $id_filtro;

        $select_filtri = isset($this->view->Filtri) ? $this->view->Filtri: \Filtri::find(
            ['conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'id ASC']
        );
        $id_filtro = new Select('id_filtro[]', $select_filtri,
            [
                'class'=>'form-control selectpicker',
                'using' => array('id','descrizione'),
                'data-style' => 'btn-flat btn-white',
                'multiple'=>'multiple',
                'data-size'=>5,
                'data-width'=>'100%',
                'data-live-search'=>true,
                'data-selected-text-format'=>'count>1',
                'useEmpty'=>false,
                'emptyText'=>'---'
            ]
        );
        $id_filtro->setLabel('Filtro');
        $fields['id_filtro'] = $id_filtro;
				
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}