<?php
namespace apps\admin\forms\filtri;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Check;

class EditForm extends Form
{

    protected $fields = [];
    protected $exclude_required = [];
    protected $custom_validation = [];

    /**
     * @param null $entity
     * @param array $options
     */
    public function initialize($entity = null, $options = [])
    {

        $auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = ['id', 'data_cambio_stato', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo'];
        $order_fields = [];

        $this->fields = $this->getAutoRenderByModel(new \Filtri(), 'Filtri', $exclude_fields, $order_fields, false);

        $select_app = isset($this->view->Applicazioni) ? $this->view->Tags : \Applicazioni::find(['conditions' => 'attivo = 1 AND id = ' . $entity->id_applicazione, 'columns' => 'id,descrizione', 'order' => 'id ASC']);
        $id_app = new Select(
            'id_applicazione',
            $select_app,
            [
                'class'                     => 'form-control selectpicker', 'using' => ['id', 'descrizione'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false,
                'emptyText'                 => '---'
            ]
        );
        $id_app->setLabel('Applicazione')->setAttribute('position', 'side');
        $this->fields['id_applicazione'] = $id_app;


        $select_gruppo_filtri = isset($this->view->FiltriGroup) ? $this->view->FiltriGroup : \FiltriGroup::find(
            ['conditions' => 'attivo = 1', 'columns' => 'id,descrizione', 'order' => 'id ASC']
        );
        $id_filtri_group = new Select('id_filtri_group', $select_gruppo_filtri,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'descrizione'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false,
                'emptyText'                 => '---'
            ]
        );
        $id_filtri_group->setLabel('Gruppo Filtri');
        $this->fields['id_filtri_group'] = $id_filtri_group;

        $select_filtro_parent = isset($this->view->FiltroParent) ? $this->view->FiltroParent : \Filtri::find(
            ['conditions' => 'attivo = 1', 'columns' => 'id,titolo', 'order' => 'id ASC']
        );

        $id_filtro_parent = new Select('id_filtro_parent', $select_filtro_parent,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => true,
                'emptyText'                 => '---'
            ]
        );
        $id_filtro_parent->setLabel('Filtro Collegato');
        $this->fields['id_filtro_parent'] = $id_filtro_parent;

        $required = new Check('required', ['class' => 'ichek']);
        $required->setLabel('Campo Obbligatorio')->setAttribute('grid_class', 'col-xs-4 text-center')->setAttribute('value', 'on');
        if ($entity->required == '1') $required->setAttribute('checked', 'checked');
        $this->fields['required'] = $required;

        $one_to_one = new Check('one_to_one', ['class' => 'ichek']);
        $one_to_one->setLabel('Cardinalità Multipla')->setAttribute('grid_class', 'col-xs-4 text-center')->setAttribute('value', 'on');
        if ($entity->one_to_one == '1') $one_to_one->setAttribute('checked', 'checked');
        $this->fields['one_to_one'] = $one_to_one;

        $frontend_filter = new Check('frontend_filter', ['class' => 'ichek']);
        $frontend_filter->setLabel('Filtro Frontend')->setAttribute('grid_class', 'col-xs-4 text-center')->setAttribute('value', 'on');
        if ($entity->frontend_filter == '1') $frontend_filter->setAttribute('checked', 'checked');
        $this->fields['frontend_filter'] = $frontend_filter;

        $this->fields['id_tipologia_stato']->setLabel('Stato filtro');
        $this->fields['id_tipologia_filtro']->setLabel('Tipologia filtro');

        $this->fields['id_tipologia_stato']->setAttribute('useEmpty', false);
        $this->fields['id_tipologia_filtro']->setAttribute('useEmpty', false);

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

        $arr_exclude_required['1'] = ['descrizione', 'required', 'one_to_one', 'frontend_filter', 'id_filtro_parent'];
        $arr_exclude_required['default'] = ['descrizione', 'required', 'one_to_one', 'frontend_filter', 'id_filtro_parent'];

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
        $id_tipologia_stato = $params['id_tipologia_stato'];

        $this->prepareValidation($id_tipologia_stato);

        $this->fields = $this->addValidateControl($this->fields, $this->custom_validation, $this->exclude_required);

    }

}
