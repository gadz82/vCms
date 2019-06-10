<?php

namespace apps\admin\forms\filtri;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form {

	public function initialize($entity=null, $options=null){
		
		$auth = $this->getAuth();
				
		// Colonne del model che non devono essere renderizzate
		$exclude_fields = array('id', 'attivo', 'id_utente', 'descrizione', 'data_creazione');
		
		// Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
		$order_fields = array();
		
		// Generazione campi
		$fields = $this->getAutoRenderByModel(new \Filtri(), 'Filtri', $exclude_fields, $order_fields, true);

        $select_gruppo_filtri = isset($this->view->FiltriGroup) ? $this->view->FiltriGroup: \FiltriGroup::find(
            ['conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'id ASC']
        );
        $id_filtri_group = new Select('id_filtri_group[]', $select_gruppo_filtri,
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
        $id_filtri_group->setLabel('Gruppo Filtri');
        $fields['id_filtri_group'] = $id_filtri_group;

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

        $select_filtro_parent = isset($this->view->FiltroParent) ? $this->view->FiltroParent: \Filtri::find(
            ['conditions'=>'attivo = 1', 'columns'=>'id,descrizione', 'order'=>'id ASC']
        );

        $id_filtro_parent = new Select('id_filtro_parent', $select_filtro_parent,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id','descrizione'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=> 5,
                'data-width'=> '100%',
                'data-live-search' => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty' => true,
                'emptyText' => '---'
            ]
        );
        $fields['id_filtro_parent'] = $id_filtro_parent;

		// Modifica valori label
		$fields['id_tipologia_stato']->setLabel('Stato filtro');
		$fields['id_tipologia_filtro']->setLabel('Tipologia filtro');
        $fields['id_filtro_parent']->setLabel('Filtro Collegato');

        $oto = new Select('ono_to_one',
            [
                "1" => "Cardinalità Singola",
                "0" => "Cardinalità Multipla",
            ],
            array('class'=>'form-control selectpicker', 'data-style' => 'btn-flat btn-white', 'multiple'=>'multiple', 'data-size'=>5, 'data-width'=>'100%','useEmpty'=>false, 'emptyText'=>'---')
        );
        $oto->setLabel('Cardinalità');
        $fields['one_to_one'] = $oto;

        $required = new Select('required',
            [
                "1" => "Obbligatorio",
                "0" => "Opzionale",
            ],
            array('class'=>'form-control selectpicker', 'data-style' => 'btn-flat btn-white', 'data-size'=>5, 'multiple'=>'multiple', 'data-width'=>'100%','data-selected-text-format'=>'count>1','useEmpty'=>false, 'emptyText'=>'---')
        );
        $required->setLabel('Obbligatorio');
        $fields['required'] = $required;

        $frontend = new Select('frontend_filter',
            [
                "1" => "Si",
                "0" => "No",
            ],
            array('class'=>'form-control selectpicker', 'data-style' => 'btn-flat btn-white', 'data-size'=>5, 'multiple'=>'multiple', 'data-width'=>'100%','data-selected-text-format'=>'count>1','useEmpty'=>false, 'emptyText'=>'---')
        );
        $frontend->setLabel('Usato come filtro');
		$fields['frontend_filter'] = $frontend;
		$fields = $this->reorderFields($fields, $order_fields);		
				
		// Aggiunge i campi al form
		foreach($fields as $name=>$field){
			$this->add($field);
		}
		
	}
	
}