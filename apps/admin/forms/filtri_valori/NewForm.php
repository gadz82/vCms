<?php

namespace apps\admin\forms\filtri_valori;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

class NewForm extends Form
{

    protected $fields = [];
    protected $exclude_required = [];
    protected $custom_validation = [];

    public function initialize($entity = null, $options = [])
    {

        $auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = ['id', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo', 'id_filtro_valore_parent', 'key'];
        $order_fields = [];

        $this->fields = $this->getAutoRenderByModel(new \FiltriValori(), 'FiltriValori', $exclude_fields, $order_fields, false);

        $select_gruppo_filtri = isset($this->view->Filtri) ? $this->view->Filtri : \Filtri::find(
            ['conditions' => 'attivo = 1', 'columns' => 'id,titolo', 'order' => 'id ASC']
        );
        $id_filtro = new Select('id_filtro', $select_gruppo_filtri,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false,
                'emptyText'                 => '---'
            ]
        );
        $id_filtro->setLabel('Filtro');
        $this->fields['id_filtro'] = $id_filtro;

        $this->fields['valore'] = new TextArea('valore', ['class' => 'form-control', 'placeholder' => 'Valori separati da virgola (la key viene generata automaticamente, per personalizzarla usare la sintassi key:valore)']);
        $this->fields['valore']->setLabel('Valore del filtro');

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

        $arr_exclude_required['1'] = ['numeric_key'];
        $arr_exclude_required['default'] = ['numeric_key'];

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
