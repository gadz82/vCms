<?php

namespace apps\admin\forms\tags;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

class NewForm extends Form
{

    protected $fields = [];
    protected $exclude_required = [];
    protected $custom_validation = [];

    public function initialize($entity = null, $options = [])
    {

        $auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = ['id', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo'];
        $order_fields = [];

        $this->fields = $this->getAutoRenderByModel(new \Tags(), 'Tags', $exclude_fields, $order_fields, false);

        $select_app = isset($this->view->Applicazioni) ? $this->view->Tags : \Applicazioni::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione', 'order' => 'descrizione ASC']);
        $id_app = new Select('id_applicazione', $select_app, ['class' => 'form-control selectpicker', 'using' => ['id', 'descrizione'], 'data-style' => 'btn-flat btn-white', 'data-size' => 5, 'data-width' => '100%', 'data-live-search' => true, 'data-selected-text-format' => 'count>1', 'useEmpty' => false, 'emptyText' => '---']);
        $id_app->setLabel('Applicazione')->setAttribute('position', 'side');
        $this->fields['id_applicazione'] = $id_app;

        $this->fields = $this->reorderFields($this->fields, $order_fields);

        /* PREPARE VALIDATION */
        $stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
        $this->prepareValidation($stato);
        /* FINE BLOCCO */

        /* ASSEGNAZIONE FIELDS->FORM */
        foreach ($this->fields as $name => $field) {
            $this->add($field);
        }

        if (isset($entity)) {
            $this->add(new Hidden('id', ['hidden' => true, 'value' => $entity->id]));
        }
        /* FINE BLOCCO */

    }

    private function prepareValidation($id_tipologia_stato)
    {

        $arr_exclude_required = [];

        $this->custom_validation = [];

        $arr_exclude_required['1'] = ['content'];
        $arr_exclude_required['default'] = ['content'];

        $this->compileValidation($id_tipologia_stato, $arr_exclude_required);

    }

    private function compileValidation($id_tipologia_stato, $arr_exclude_required)
    {

        $exclude_required = false;

        foreach ($arr_exclude_required as $key => $val) {
            if (in_array($id_tipologia_stato, explode('|', $key))) {
                $exclude_required = $val;
                break;
            }
        }

        $this->exclude_required = !$exclude_required ? $arr_exclude_required['default'] : $exclude_required;

        $arr_render_required = [];
        $render = array_keys($this->fields);

        foreach ($arr_exclude_required as $key => $val) {
            $render_required = array_diff($render, $val);
            sort($render_required);

            $arr_render_required[$key] = $render_required;
        }

        $this->getDi()->getView()->render_required = json_encode($arr_render_required);

    }

    public function beforeValidation()
    {

        $params = $this->request->getPost();
        $id_tipologia_stato = 1;

        $this->prepareValidation($id_tipologia_stato);

        $this->fields = $this->addValidateControl($this->fields, $this->custom_validation, $this->exclude_required);

    }

}
