<?php

namespace apps\admin\forms\form_requests;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Email;
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
        $fields = $this->getAutoRenderByModel(new \FormRequests(), 'FormRequests', $exclude_fields, $order_fields, true);

        $select_posts = isset($this->view->Posts) ? $this->view->Posts : \Posts::find(['conditions' => 'attivo = 1', 'columns' => 'id,titolo', 'order' => 'id ASC']);
        $id_post = new Select('id_post[]', $select_posts, [
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

        $id_post->setLabel('Contenuto Collegato');
        $fields['id_post'] = $id_post;

        $select_forms = isset($this->view->Forms) ? $this->view->Forms : \Forms::find(['conditions' => 'attivo = 1', 'columns' => 'id,titolo', 'order' => 'id ASC']);
        $id_form = new Select('id_form[]', $select_forms, [
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

        $id_form->setLabel('Form');
        $fields['id_form'] = $id_form;

        $letto = new Select('letto', [
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
        $letto->setLabel('Letto');
        $fields['letto'] = $letto;

        $fields['email'] = new Email(
            'email',
            [
                'class'       => 'form-control',
                'placeholder' => 'Email',
            ]
        );
        $fields['email']->setLabel('Email');

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}