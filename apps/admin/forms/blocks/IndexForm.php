<?php

namespace apps\admin\forms\blocks;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'content', 'data_aggiornamento', 'data_creazione');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Blocks(), 'Blocks', $exclude_fields, $order_fields, true);

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

        $select_block_tag = isset($this->view->BlocksTags) ? $this->view->BlocksTags: \BlocksTags::find(array('conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'descrizione ASC'));
        $id_bt = new Select(
            'id_block_tag',
            $select_block_tag,
            [
                'class'=>'form-control selectpicker', 'using' => ['id','descrizione'],
                'data-style' => 'btn-flat btn-white',
                'data-size'=>5,
                'data-width'=>'100%',
                'multiple' => 'multiple',
                'data-actions-box' => true,
                'data-live-search'=>true,
                'data-selected-text-format'=>'count>1'
            ]
        );
        $id_bt->setLabel('Tag Blocco');
        $fields['id_block_tag'] = $id_bt;
	
		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato block');
		$fields['id_tipologia_block']->setLabel('Tipologia block');

				
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}