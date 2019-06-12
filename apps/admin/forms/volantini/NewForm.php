<?php

namespace apps\admin\forms\volantini;

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

        $exclude_fields = ['id', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo', 'files_path'];
        $order_fields = [];

        $this->fields = $this->getAutoRenderByModel(new \Volantini(), 'Volantini', $exclude_fields, $order_fields, false);


        $this->fields['id_tipologia_stato']->setLabel('Stato volantino')->setAttribute('grid_class', 'col-sm-6');
        $this->fields['id_tipologia_volantino']->setLabel('Tipologia volantino')->setAttribute('grid_class', 'col-sm-6');

        $this->fields['id_tipologia_stato']->setAttribute('useEmpty', false);
        $this->fields['id_tipologia_volantino']->setAttribute('useEmpty', false);

        $this->fields['data_inizio_pubblicazione']->setAttribute('class', 'form-control date-format');
        $this->fields['data_fine_pubblicazione']->setAttribute('class', 'form-control date-format');

        $select_regioni = isset($this->view->Regioni) ? $this->view->Regioni : \Regioni::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione']);
        $regioni = new Select('id_regione', $select_regioni,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'descrizione'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'emptyText'                 => 'Seleziona regione...',
                'useEmpty'                  => true
            ]
        );
        $this->fields['id_regione'] = $regioni;
        $this->fields['id_regione']->setLabel('Regione');

        $select_pdv = isset($this->view->PuntiVendita) ? $this->view->PuntiVendita : \PuntiVendita::find(['conditions' => 'attivo = 1 AND id_tipologia_punto_vendita = 4', 'columns' => 'id, nome']);
        $pdv = new Select('id_punto_vendita', $select_pdv,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'nome'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 15,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'emptyText'                 => '---',
                'useEmpty'                  => true
            ]
        );
        $this->fields['id_punto_vendita'] = $pdv;
        $this->fields['id_punto_vendita']->setLabel('Punto vendita');

        $this->fields = $this->reorderFields($this->fields, $order_fields);


        /* PREPARE VALIDATION */
        $stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
        $this->prepareValidation($stato);
        /* FINE BLOCCO */
        $this->fields['id_punto_vendita']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['id_regione']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['numero']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['anno']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['data_inizio_pubblicazione']->setAttribute('grid_class', 'col-sm-6');
        $this->fields['data_fine_pubblicazione']->setAttribute('grid_class', 'col-sm-6');

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

        $arr_exclude_required['1'] = ['id_punto_vendita'];
        $arr_exclude_required['default'] = ['id_punto_vendita'];

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
