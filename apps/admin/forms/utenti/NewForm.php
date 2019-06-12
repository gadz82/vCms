<?php

namespace apps\admin\forms\utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Identical;

class NewForm extends Form
{
    public function initialize($entity = null, $options = null)
    {

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = [
            'id',
            'token',
            'avatar',
            'data_creazione',
            'data_aggiornamento',
            'data_creazione_token',
            'attivo'
        ];

        // Ordine delle colonne del model. Se non non presenti tutte, quelle escluse vengono aggiunte in coda nell'ordine presente nel model
        $order_fields = [
            'nome',
            'cognome',
            'nome_utente',
            'password',
            'email',
            'avatar'
        ];

        // Generazione campi
        $fields = $this->getAutoRenderByModel(new \Utenti (), 'Utenti', $exclude_fields, $order_fields, false);

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
        $id_ruolo = new Select ('id_ruolo', $select_ruolo, [
            'class'                     => 'form-control selectpicker',
            'using'                     => [
                'id',
                'descrizione'
            ],
            'data-size'                 => 5,
            'data-width'                => '100%',
            'data-live-search'          => true,
            'data-selected-text-format' => 'count>1',
            'useEmpty'                  => true,
            'emptyText'                 => '---'
        ]);
        $id_ruolo->setLabel('Ruolo');
        $fields ['id_ruolo'] = $id_ruolo;

        /*
         * Riordinamento campi.
         *
         * ATTENZIONE
         * Il metodo deve essere eseguito prima dell'aggiunta di eventuali campi personalizzati non presenti nel model.
         * In caso contrario i campi personalizzati non verranno renderizzati
         */
        $fields = $this->reorderFields($fields, $order_fields);

        // Attiva il controllo di validazione per i campi non disabled
        $fields = $this->addValidateControl($fields);

        $csrf = new Hidden ('csrf', [
            'hidden' => true
        ]);
        $csrf->addValidator(new Identical ([
            'value'   => $this->security->getSessionToken(),
            'message' => _('Errore validazione CSRF')
        ]));
        $csrf->clear();
        $fields ['csrf'] = $csrf;

        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            $this->add($field);
        }

        $this->add(new Hidden ("id", [
            'hidden' => true
        ]));
    }
}
