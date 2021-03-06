<?php

namespace apps\admin\forms\ruoli_menu;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzatea
        $exclude_fields = ['id', 'attivo', 'id_utente', 'descrizione', 'class', 'header', 'visible', 'azione', 'data_creazione', 'data_aggiornamento', 'ordine'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \RuoliMenu(), 'RuoliMenu', $exclude_fields, $order_fields, true);

        $select_ruoli = isset($this->view->Ruoli) ? $this->view->Ruoli : \Ruoli::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione']);

        $ruoli = new Select('id_ruolo[]', $select_ruoli,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'descrizione'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'multiple'                  => true,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false
            ]
        );
        $fields['id_ruolo'] = $ruoli;
        $fields['id_ruolo']->setLabel('Ruolo');

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}