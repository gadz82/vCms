<?php

namespace apps\admin\forms\posts;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){

		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'testo', 'excerpt');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();

        $id_tipologia_post = $this->getDI()->getSession()->get('id_tipologia_post_current');
        $id_app = $this->getDI()->getSession()->get('id_app');

        if(!empty($id_tipologia_post)){
            $exclude_fields[] = 'id_tipologia_post';
        }
        if(!empty($id_app)){
            $exclude_fields[] = 'id_applicazione';
        }
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Posts(), 'Posts', $exclude_fields, $order_fields, true);

		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato post');
        if(empty($id_tipologia_post)){
            $fields['id_tipologia_post']->setLabel('Tipologia post');
        }

        if(empty($id_app)){
            $select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni : \Applicazioni::find(array('conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'id ASC'));
            $id_applicazione = new Select(
                'id_applicazione',
                $select_app,
                [
                    'class'=>'form-control selectpicker',
                    'using' => ['id','descrizione'],
                    'data-style' => 'btn-flat btn-white',
                    'data-size'=>15,
                    'data-width'=>'100%',
                    'data-live-search'=> true,
                    'data-actions-box' => 1,
                    'data-selected-text-format'=>'count>1',
                    'multiple' => true
                ]
            );

            $fields['id_applicazione'] = $id_applicazione;
            $fields['id_applicazione']->setLabel('Applicazione');

        }
		$fields = $this->reorderFields($fields, $order_fields);
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}