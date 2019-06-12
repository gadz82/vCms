<?php

namespace apps\admin\forms\punti_vendita;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo', 'id_utente', 'data', 'lat', 'lng', 'address', 'data_creazione', 'data_aggiornamento', 'comune'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \PuntiVendita(), 'PuntiVendita', $exclude_fields, $order_fields, true);

        // Modifica valori label
        $fields['id_tipologia_stato']->setLabel('Stato punto vendita');
        $fields['id_tipologia_punto_vendita']->setLabel('Tipologia punto vendita');
        $select_regioni = isset($this->view->Regioni) ? $this->view->Regioni : \Regioni::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione']);
        $regioni = new Select('id_regione[]', $select_regioni,
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
        $fields['id_regione'] = $regioni;
        $fields['id_regione']->setLabel('Regione');

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}