<?php

namespace apps\admin\forms\utenti;

use apps\admin\forms\Form;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\Identical;

class EditForm extends Form
{
    public function initialize($entity = null, $options = null)
    {

        // Colonne del model che non devono essere renderizzate
        $exclude_fields = [
            'id',
            'token',
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

        $fields ['data_creazione']->setAttribute('disabled', true);
        $fields ['data_aggiornamento']->setAttribute('disabled', true);

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
            'data-style'                => 'btn-flat btn-white',
            'data-size'                 => 5,
            'data-width'                => '100%',
            'data-live-search'          => true,
            'data-selected-text-format' => 'count>1',
            'useEmpty'                  => false,
            'emptyText'                 => '---'
        ]);
        $id_ruolo->setLabel('Ruolo');
        $fields ['id_ruolo'] = $id_ruolo;

        $avatar_list = [];
        $file = array_diff(scandir('img/avatar'), [
            '.',
            '..'
        ]);
        foreach ($file as $f) {
            if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), [
                'png'
            ], true))
                $avatar_list ['avatar/' . $f] = $f;
        }

        $avatar = new Select ('avatar', $avatar_list, [
            'class'                     => 'form-control selectpicker',
            'using'                     => [
                'id',
                'descrizione'
            ],
            'data-style'                => 'btn-flat btn-white',
            'data-size'                 => 5,
            'data-width'                => '100%',
            'data-live-search'          => true,
            'data-selected-text-format' => 'count>1',
            'useEmpty'                  => false,
            'emptyText'                 => '---'
        ]);
        $avatar->setLabel('Avatar');
        $fields ['avatar'] = $avatar;

        $fields = $this->reorderFields($fields, $order_fields);

        // Attiva il controllo di validazione per i campi non disabled
        $custom_validation = [];
        $exclude_required = [];
        $fields = $this->addValidateControl($fields, $custom_validation, $exclude_required);

        $csrf = new Hidden ('csrf', [
            'hidden' => true
        ]);
        $csrf->addValidator(new Identical ([
            'value'   => $this->security->getSessionToken(),
            'message' => _('Errore validazione CSRF')
        ]));
        $csrf->clear();
        $fields ['csrf'] = $csrf;

        $ajax = (isset ($options ['ajax']) && $options ['ajax']) ? true : false;
        // Aggiunge i campi al form
        foreach ($fields as $name => $field) {
            if ($ajax) {
                if (in_array($field->getName(), $options ['fields'])) {
                    $this->add($field);
                }
            } else {
                $this->add($field);
            }
        }

        $this->add(new Hidden ("id", [
            'hidden' => true
        ]));
    }
}
