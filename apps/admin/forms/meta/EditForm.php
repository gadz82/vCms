<?php

namespace apps\admin\forms\meta;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;

class EditForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');
		
		$exclude_fields = array('id','data_cambio_stato','data_creazione','data_aggiornamento','id_utente','attivo');
		$order_fields = array();
		$this->fields = $this->getAutoRenderByModel(new \Meta(), 'Meta', $exclude_fields, $order_fields, false);
				
		$this->fields = $this->reorderFields($this->fields, $order_fields);
		$select_gruppo_meta = isset($this->view->MetaGroup) ? $this->view->MetaGroup: \MetaGroup::find(array('conditions'=>'attivo = 1','columns'=>'id,descrizione','order'=>'id ASC'));
		$id_meta_group = new Select('id_meta_group', $select_gruppo_meta, array('class'=>'form-control selectpicker', 'using' => array('id','descrizione'), 'data-style' => 'btn-flat btn-white', 'data-size'=>5, 'data-width'=>'100%', 'data-live-search'=>true, 'data-selected-text-format'=>'count>1', 'useEmpty'=>false, 'emptyText'=>'---'));
		$id_meta_group->setLabel('Gruppo Meta');
		$this->fields['id_meta_group'] = $id_meta_group;

        $this->fields['id_meta_group']->setAttribute('grid_class', 'col-sm-6');
		$this->fields['id_tipologia_meta']->setLabel('Tipologia Meta')->setAttribute('grid_class', 'col-sm-6');

        $required = new Check('required', array('class' => 'ichek'));
		if($entity->required) $required->setAttribute('checked', 'checked');
		$required->setLabel('Campo Obbligatorio')->setAttribute('value', 'on')->setAttribute('grid_class', 'col-sm-6');
		$this->fields['required'] = $required;


        $hidden = new Check('hidden', array('class' => 'ichek'));
        if($entity->hidden) $hidden->setAttribute('checked', 'checked');
        $hidden->setLabel('Nascosto')->setAttribute('value', 'on')->setAttribute('grid_class', 'col-sm-6');
        $this->fields['hidden'] = $hidden;

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
		}
		/* FINE BLOCCO */
		
	}
	
	private function prepareValidation($id_tipologia_stato){

		$arr_exclude_required = array();

		$this->custom_validation = array();

		$arr_exclude_required['1'] = array('nota','nota_new', 'dataset');
		$arr_exclude_required['default'] = array('nota','nota_new', 'dataset');

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
		$id_tipologia_stato = 1;

		$this->prepareValidation($id_tipologia_stato);

		$this->fields = $this->addValidateControl($this->fields, $this->custom_validation, $this->exclude_required);

	}
	
}
