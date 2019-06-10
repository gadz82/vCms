<?php

namespace apps\admin\forms\punti_vendita;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;

class NewForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');
		
		$exclude_fields = array('id','data_creazione','data_aggiornamento','id_utente','attivo');
		$order_fields = array();
		
		$this->fields = $this->getAutoRenderByModel(new \PuntiVendita(), 'PuntiVendita', $exclude_fields, $order_fields, false);

        $select_regioni = isset($this->view->Regioni) ? $this->view->Regioni: \Regioni::find(['conditions'=>'attivo = 1','columns'=>'id,descrizione']);
        $regioni = new Select('id_regione', $select_regioni,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id','descrizione'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=> 5,
                'data-width'=> '100%',
                'data-live-search' => true,
                'data-selected-text-format' => 'count>1',
                'emptyText' => 'Seleziona regione...',
                'useEmpty' => true
            ]
        );
        $this->fields['id_regione'] = $regioni;
        $this->fields['id_regione']->setLabel('Regione')->setAttribute('grid_class', 'col-sm-6');


		$this->fields['id_tipologia_stato']->setLabel('Stato punto vendita')->setAttribute('grid_class', 'col-sm-6');
		$this->fields['id_tipologia_punto_vendita']->setLabel('Tipologia punto vendita')->setAttribute('grid_class', 'col-sm-6');;
		
		$this->fields['id_tipologia_stato']->setAttribute('useEmpty', false);
		$this->fields['id_tipologia_punto_vendita']->setAttribute('useEmpty', false);
				
		$this->fields = $this->reorderFields($this->fields, $order_fields);
		
		/* PREPARE VALIDATION */
		$stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
		$this->prepareValidation($stato);
		/* FINE BLOCCO */
        $this->fields['comune']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['nome']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['data']->setAttribute('grid_class', 'col-sm-6')->setLabel('Descrizione Insegna');
        $this->fields['lat']->setAttribute('grid_class', 'col-sm-6')->setLabel('Latitudine');
        $this->fields['lng']->setAttribute('grid_class', 'col-sm-6')->setLabel('Longitudine');
        $this->fields['lng']->setAttribute('grid_class', 'col-sm-6')->setLabel('Longitudine');
        $this->fields['id_pdv']->setAttribute('grid_class', 'col-sm-6')->setLabel('Id Conad PDV');
        $this->fields['coop']->setAttribute('grid_class', 'col-sm-6')->setLabel('Cooperativa');
        $this->fields['address']->setLabel('Indirizzo');
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

		$arr_exclude_required['1'] = array();
		$arr_exclude_required['default'] = array();

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
