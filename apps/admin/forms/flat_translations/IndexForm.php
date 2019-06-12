<?php

namespace apps\admin\forms\flat_translations;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo', 'id_utente'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \FlatTranslations(), 'FlatTranslations', $exclude_fields, $order_fields, true);

        $select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni : \Applicazioni::find(['conditions' => 'attivo = 1', 'columns' => 'id,CONCAT(titolo, " - ", codice) AS titolo', 'order' => 'id ASC']);
        $id_app = new Select('id_applicazione[]', $select_app, [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-style'                => 'btn-flat btn-white',
                'multiple'                  => 'multiple',
                'data-size'                 => 5,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-actions-box'          => 1,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false,
                'emptyText'                 => '---']
        );

        $id_app->setLabel('Applicazione');
        $fields['id_applicazione'] = $id_app;

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}