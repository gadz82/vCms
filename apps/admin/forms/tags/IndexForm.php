<?php

namespace apps\admin\forms\tags;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = array('content', 'attivo');

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = array();

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Tags(), 'Tags', $exclude_fields, $order_fields, true);

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}