<?php

namespace apps\admin\forms\forms;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\Email as EmailValidator;

class EditForm extends Form {
	
	protected $fields = array();
	protected $exclude_required = array();
	protected $custom_validation = array();
	
	public function initialize($entity=null, $options=array()){
		
		$auth = $this->getDI()->getSession()->get('auth-identity');

		$exclude_fields = array('id','data_creazione','data_aggiornamento','id_utente','attivo');
		$order_fields = array('titolo', 'key', 'email_to');
		
		$this->fields = $this->getAutoRenderByModel(new \Forms(), 'Forms', $exclude_fields, $order_fields, false);
		
		$this->fields['id_tipologia_stato']->setLabel('Stato form');
		$this->fields['id_tipologia_form']->setLabel('Tipologia form');
		
		$this->fields['id_tipologia_stato']->setAttribute('useEmpty', false)->setAttribute('grid_class', 'col-sm-6');
		$this->fields['id_tipologia_form']->setAttribute('useEmpty', false)->setAttribute('grid_class', 'col-sm-6');

        $select_app = isset($this->view->Applicazioni) ? $this->view->Tags: \Applicazioni::find(array('conditions'=>'attivo = 1 AND id = '. $entity->id_applicazione,'columns'=>'id,descrizione','order'=>'descrizione ASC'));
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
                'useEmpty'=>false,
                'emptyText'=>'---'
            ]
        );
        $id_app->setLabel('Applicazione')->setAttribute('position', 'side');
        $this->fields['id_applicazione'] = $id_app;


        $this->fields['key']->setAttribute('grid_class', 'col-sm-6');
		$this->fields['email_to']->setAttribute('grid_class', 'col-sm-6');

        $this->fields['email_cc']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['email_bcc']->setAttribute('grid_class', 'col-sm-6');

        $invio_utente = new Check('invio_utente', array('class' => 'ichek'));
        if($entity->invio_utente) $invio_utente->setAttribute('checked', 'checked');
        $invio_utente->setLabel('Invia copia all\'utente. *Obbligatorio campo email.')->setAttribute('value', 'on')->setAttribute('grid_class', 'col-xs-12');
        $this->fields['invio_utente'] = $invio_utente;
				
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

        $arr_exclude_required['1'] = ['email_to', 'email_bcc', 'email_cc', 'testo'];
        $arr_exclude_required['default'] = ['email_to', 'email_bcc', 'email_cc', 'testo'];

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
        $this->custom_validation['email_to'] = new EmailValidator(
            ['message' => 'Email non valida']
        );
		$this->fields = $this->addValidateControl($this->fields, $this->custom_validation, $this->exclude_required);

	}
	
}
