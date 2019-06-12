<?php

namespace apps\admin\forms\meta;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo', 'id_utente', 'data_creazione', 'dataset'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];
        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Meta(), 'Meta', $exclude_fields, $order_fields, true);

        $select_gruppo_meta = isset($this->view->MetaGroup) ? $this->view->MetaGroup : \MetaGroup::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione', 'order' => 'id ASC']);
        $id_meta_group = new Select('id_meta_group[]', $select_gruppo_meta, ['class' => 'form-control selectpicker', 'using' => ['id', 'descrizione'], 'data-style' => 'btn-flat btn-white', 'multiple' => 'multiple', 'data-size' => 5, 'data-width' => '100%', 'data-live-search' => true, 'data-selected-text-format' => 'count>1', 'useEmpty' => false, 'emptyText' => '---']);
        $id_meta_group->setLabel('Gruppo Meta');
        $fields['id_meta_group'] = $id_meta_group;

        $fields['id_tipologia_meta']->setLabel('Tipologia Meta');
        $fields['key']->setLabel('Slug Meta');
        $fields['label']->setLabel('Etichetta Meta');

        $required = new Select('required', [
            '1' => 'Si',
            '0' => 'No'
        ],
            [
                'class'      => 'form-control selectpicker',
                'data-style' => 'btn-flat btn-white',
                'data-width' => '100%',
                'useEmpty'   => true,
                'emptyText'  => '---'
            ]);
        $required->setLabel('Campo Obbligatorio');
        $fields['required'] = $required;

        $hidden = new Select('hidden', [
            '1' => 'Si',
            '0' => 'No'
        ],
            [
                'class'      => 'form-control selectpicker',
                'data-style' => 'btn-flat btn-white',
                'data-width' => '100%',
                'useEmpty'   => true,
                'emptyText'  => '---'
            ]);
        $hidden->setLabel('Nascosto');
        $fields['hidden'] = $hidden;

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}