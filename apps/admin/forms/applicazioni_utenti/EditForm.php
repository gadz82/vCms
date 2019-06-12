<?php

namespace apps\admin\forms\applicazioni_utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;

class EditForm extends Form
{

    protected $fields = [];
    protected $exclude_required = [];
    protected $custom_validation = [];

    public function initialize($entity = null, $options = [])
    {

        $auth = $this->getDI()->getSession()->get('auth-identity');

        $exclude_fields = ['id', 'attivo'];
        $order_fields = [];

        $this->fields = $this->getAutoRenderByModel(new \ApplicazioniUtenti(), 'ApplicazioniUtenti', $exclude_fields, $order_fields, false);

        $this->fields = $this->reorderFields($this->fields, $order_fields);


        /* ASSEGNAZIONE FIELDS->FORM */
        foreach ($this->fields as $name => $field) {
            $this->add($field);
        }

        if (isset($entity)) {
            $this->add(new Hidden('id', ['hidden' => true, 'value' => $entity->id]));
        }
        /* FINE BLOCCO */

    }


}
