<?php

namespace apps\admin\forms\users;

use apps\admin\forms\Form;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

class IndexForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        $auth = $this->getAuth();

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = ['id', 'attivo', 'id_utente', 'validation_token', 'token_validated', 'password', 'validation_expiration_date', 'reset_password_expiration_date', 'password_reset_token', 'localita', 'indirizzo', 'data_aggiornamento', 'cap'];


        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Users(), 'Users', $exclude_fields, $order_fields, true);

        // Modifica valori label
        $fields['id_tipologia_stato']->setLabel('Stato utente sito');

        $select_groups = isset($this->view->UsersGroups) ? $this->view->UsersGroups : \UsersGroups::find(['conditions' => 'attivo = 1', 'columns' => 'id,titolo']);

        $groups = new Select('id_users_groups[]', $select_groups,
            [
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-style'                => 'btn-flat btn-white',
                'data-size'                 => 5,
                'multiple'                  => true,
                'data-width'                => '100%',
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'useEmpty'                  => false
            ]
        );
        $fields['id_users_groups'] = $groups;
        $fields['id_users_groups']->setLabel('Gruppo Utenti');
        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

    }

}