<?php

namespace apps\admin\forms\applicazioni_utenti;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo'];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \ApplicazioniUtenti(), 'ApplicazioniUtenti', $exclude_fields, $order_fields, true);

        $select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni : \Applicazioni::find(['conditions' => 'attivo = 1', 'columns' => 'id,descrizione', 'order' => 'descrizione ASC']);
        $id_app = new Select('id_applicazione[]', $select_app, ['class' => 'form-control selectpicker', 'using' => ['id', 'descrizione'], 'data-style' => 'btn-flat btn-white', 'multiple' => 'multiple', 'data-size' => 5, 'data-width' => '100%', 'data-live-search' => true, 'data-selected-text-format' => 'count>1', 'useEmpty' => false, 'emptyText' => '---']);
        $id_app->setLabel('Applicazione');
        $fields['id_applicazione'] = $id_app;

        $select_utente = isset($this->view->Utenti) ? $this->view->Utenti : \Utenti::find(['conditions' => 'attivo = 1', 'columns' => 'id,CONCAT(nome," ", cognome ) AS descrizione', 'order' => 'id ASC']);
        $id_utente = new Select('id_utente_applicazione[]', $select_utente, ['class' => 'form-control selectpicker', 'using' => ['id', 'descrizione'], 'data-style' => 'btn-flat btn-white', 'multiple' => 'multiple', 'data-size' => 5, 'data-width' => '100%', 'data-live-search' => true, 'data-selected-text-format' => 'count>1', 'useEmpty' => false, 'emptyText' => '---']);
        $id_utente->setLabel('Utente');
        $fields['id_utente_applicazione'] = $id_utente;

        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}