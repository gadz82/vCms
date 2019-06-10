<?php

namespace apps\admin\forms\files;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;

class EditForm extends Form
{

    protected $fields = array();
    protected $exclude_required = array();
    protected $custom_validation = array();

    public function initialize($entity = null, $options = array())
    {

        $auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = array('id', 'data_cambio_stato', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo', 'private');
        $order_fields = array();

        $this->fields = $this->getAutoRenderByModel(new \Files(), 'Files', $exclude_fields, $order_fields, false);
        foreach ($this->fields as $field) {

            if (!in_array($field->getName(), ['alt', 'priorita'])) {
                $field->setAttribute('disabled', 'disabled');
            }
        }
        $this->fields = $this->reorderFields($this->fields, $order_fields);

        $select_groups = isset($this->view->filesUsersGroups) ? $this->view->filesUsersGroups : \UsersGroups::find([
            'conditions' => 'attivo = 1',
            'columns' => 'id,titolo',
            'order' => 'id ASC'
        ]);

        $ug = new Select(
            'filesUsersGroups',
            $select_groups,
            [
                'name' => 'filesUsersGroups',
                'class' => 'form-control selectpicker',
                'using' => ['id', 'titolo'],
                'data-size' => 15,
                'data-width' => '100%',
                'required' => false,
                'data-selected-text-format' => 'count>1',
                'useEmpty' => true,
                'emptyValue' => 0,
                'emptyText' => 'Tutti'
            ]
        );
        $this->fields['filesUsersGroups'] = $ug;
        $this->fields['filesUsersGroups']->setLabel('Scegli i gruppi utenti autorizzati');

        /* PREPARE VALIDATION */
        $stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
        $this->prepareValidation($stato);
        /* FINE BLOCCO */

        /* ASSEGNAZIONE FIELDS->FORM */
        foreach ($this->fields as $name => $field) {
            $this->add($field);
        }

        if (isset($entity)) {
            $this->add(new Hidden('id', array('hidden' => true, 'value' => $entity->id)));
        }
        /* FINE BLOCCO */
    }

    private function prepareValidation($id_tipologia_stato)
    {

        $arr_exclude_required = array();

        $this->custom_validation = array();

        $arr_exclude_required['1'] = array('nota', 'nota_new');
        $arr_exclude_required['default'] = array('nota', 'nota_new');

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

        $arr_render_required = array();
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
