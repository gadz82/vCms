<?php

namespace apps\admin\forms\files;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IframeForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo', 'id_utente', 'data_aggiornamento', 'orginal_filename', 'filename', 'filetype', 'filepath', 'fileurl', 'filesize', 'priorita', 'private'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Files(), 'Files', $exclude_fields, $order_fields, true);
        // Modifica valori label
        $fields['id_tipologia_stato']->setLabel('Stato file');
        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}