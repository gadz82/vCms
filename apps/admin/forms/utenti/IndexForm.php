<?php

namespace apps\admin\forms\utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Select;

class IndexForm extends Form
{
    public function initialize($entity = null, $options = null)
    {

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = [
            'id',
            'password',
            'avatar',
            'token',
            'data_creazione_token',
            'id_ruolo',
            'attivo'
        ];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [
            'nome',
            'cognome',
            'nome_utente'
        ];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Utenti (), 'Utenti', $exclude_fields, $order_fields, true);

        // Modifica valori label
        $fields ['id_tipologia_stato']->setLabel('Stato utente');
        $fields ['id_tipologia_utente']->setLabel('Tipologia utente');

        // Creazione campo personalizzato
        $select_ruolo = isset ($this->view->Ruoli) ? $this->view->Ruoli : \Ruoli::find([
            'conditions' => 'attivo = 1',
            'columns'    => 'id,descrizione',
            'order'      => 'descrizione ASC',
            'cache'      => [
                'key'      => 'UtentiEdit-Ruolo-find',
                'lifetime' => 86400
            ]
        ]);
        $id_ruolo = new Select ('id_ruolo[]', $select_ruolo, [
            'class'                     => 'form-control selectpicker',
            'using'                     => [
                'id',
                'descrizione'
            ],
            'data-style'                => 'btn-flat btn-white',
            'multiple'                  => true,
            'data-actions-box'          => true,
            'data-size'                 => 5,
            'data-width'                => '100%',
            'data-live-search'          => true,
            'data-selected-text-format' => 'count>1',
            'useEmpty'                  => false,
            'emptyText'                 => '---'
        ]);
        $id_ruolo->setLabel('Ruolo');
        $fields['id_ruolo'] = $id_ruolo;

        /*
         * Riordinamento campi.
         *
         * ATTENZIONE
         * Il metodo deve essere eseguito prima dell'aggiunta di eventuali campi personalizzati non presenti nel model.
         * In caso contrario i campi personalizzati non verranno renderizzati
         */
        $fields = $this->reorderFields($fields, $order_fields);

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }
    }
}
