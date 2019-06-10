<?php

namespace apps\admin\forms\volantini;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'files_path', 'data_creazione', 'data_aggiornamento');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Volantini(), 'Volantini', $exclude_fields, $order_fields, true);
	
		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato volantino');
		$fields['id_tipologia_volantino']->setLabel('Tipologia volantino');
		$fields['id_tipologia_punto_vendita']->setLabel('Tipologia Punto Vendita');
        $select_regioni = isset($this->view->Regioni) ? $this->view->Regioni: \Regioni::find(['conditions'=>'attivo = 1','columns'=>'id,nome']);
        $regioni = new Select('id_regione[]', $select_regioni,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id','descrizione'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=> 5,
                'multiple' => true,
                'data-width'=> '100%',
                'data-live-search' => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty' => false
            ]
        );
        $fields['id_regione'] = $regioni;
        $fields['id_regione']->setLabel('Regione');

        $select_pdv = isset($this->view->PuntiVendita) ? $this->view->PuntiVendita: \PuntiVendita::find(['conditions'=>'attivo = 1 AND id_tipologia_punto_vendita = 4','columns'=>'id,nome']);
        $pdv = new Select('id_punto_vendita[]', $select_pdv,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id','nome'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=>15,
                'data-width'=>'100%',
                'data-live-search'=> true,
                'data-actions-box' => 1,
                'data-selected-text-format'=>'count>1',
                'multiple' => true,
            ]
        );
        $fields['id_punto_vendita'] = $pdv;
        $fields['id_punto_vendita']->setLabel('Punto vendita');
				
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}