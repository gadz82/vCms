<?php

namespace apps\admin\forms\ruoli_menu;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

class EditForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = array('id','data_creazione','data_aggiornamento','id_utente','attivo', 'visible', 'header');
		$order_fields = array();
		
		$this->fields = $this->getAutoRenderByModel(new \RuoliMenu(), 'RuoliMenu', $exclude_fields, $order_fields, false);
        $select_ruoli = isset($this->view->Ruoli) ? $this->view->Ruoli: \Ruoli::find();

        $ruoli = new Select('id_ruolo', $select_ruoli,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id','descrizione'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=> 5,
                'data-width'=> '100%',
                'data-live-search' => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty' => false
            ]
        );
        $this->fields['id_ruolo'] = $ruoli;
        $this->fields['id_ruolo']->setLabel('Ruolo');


        $select_ruoli_menu = isset($this->view->RuoliMenu) ? $this->view->RuoliMenu : \RuoliMenu::query()
            ->columns('RuoliMenu.id, CONCAT(RuoliMenu.id," - ", r.descrizione, " - ", RuoliMenu.descrizione) AS descrizione')
            ->innerJoin('Ruoli', 'r.id = RuoliMenu.id_ruolo', 'r')
            ->where('RuoliMenu.attivo = 1')
            ->orderBy('RuoliMenu.id_padre, RuoliMenu.ordine')
            ->execute();

        $id_padre = new Select('id_padre', $select_ruoli_menu,
            [
                'class' => 'form-control selectpicker',
                'using' => array('id', 'descrizione'),
                'data-style' => 'btn-flat btn-white',
                'data-size'=> 12,
                'data-width'=> '100%',
                'data-live-search' => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty' => true,
                'emptyText' => '---'
            ]
        );
        $this->fields['id_padre'] = $id_padre;
        $this->fields['id_padre']->setLabel('Menu Parent');


        $this->fields = $this->reorderFields($this->fields, $order_fields);
		
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

		$arr_exclude_required['1'] = array('risorsa', 'azione', 'id_padre');
		$arr_exclude_required['default'] = array('risorsa', 'azione', 'id_padre');

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
