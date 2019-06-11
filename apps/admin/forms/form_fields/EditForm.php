<?php

namespace apps\admin\forms\form_fields;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;

class EditForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = array('id','data_creazione','data_aggiornamento','id_utente','attivo', 'id_form');
		$order_fields = array();
		
		$this->fields = $this->getAutoRenderByModel(new \FormFields(), 'FormFields', $exclude_fields, $order_fields, false);
		

		$this->fields = $this->reorderFields($this->fields, $order_fields);

		$this->fields['id_tipologia_stato']->setLabel('Stato campo form');
		$this->fields['id_tipologia_form_fields']->setLabel('Tipologia campo form');

		$this->fields['id_tipologia_stato']->setAttribute('useEmpty', false);
		$this->fields['id_tipologia_form_fields']->setAttribute('useEmpty', false);

        $obbligatorio = new Check('obbligatorio', array('class' => 'ichek'));
        if($entity->obbligatorio) $obbligatorio->setAttribute('checked', 'checked');
        $obbligatorio->setLabel('Obbligatorio')->setAttribute('value', 'on')->setAttribute('grid_class', 'col-xs-12');
        $this->fields['obbligatorio'] = $obbligatorio;

		/* PREPARE VALIDATION */
		$stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
		$this->prepareValidation($stato);
		/* FINE BLOCCO */

		/* ASSEGNAZIONE FIELDS->FORM */
		foreach($this->fields as $name=>$field){
			$this->add($field);
		}
		
		if(isset($entity)){
			$this->add(new Hidden('id', array('hidden'=>true, 'value' => $entity->id)));
            $this->add(new Hidden('id_form', array('hidden'=>true, 'value' => $entity->id_form)));
		}
	}
	
	private function prepareValidation($id_tipologia_stato){

		$arr_exclude_required = array();

		$this->custom_validation = array();

        $arr_exclude_required['1'] = array('value', 'placeholder');
        $arr_exclude_required['default'] = array('value', 'placeholder');

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