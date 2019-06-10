<?php

class Forms extends BaseModel
{
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_applicazione;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_form;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $testo;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $key;

    /**
     *
     * @var string
     * @Column(type="string", length=175, nullable=true)
     */
    public $email_to;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $email_cc;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $email_bcc;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $submit_label;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_creazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_aggiornamento;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id_utente;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $attivo;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $invio_utente;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Forms[]|Forms
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Forms
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->hasMany('id', 'FormFields', 'id_form', ['alias' => 'FormFields', 'reusable' => true]);
        $this->hasMany('id', 'FormRequests', 'id_form', ['alias' => 'FormRequests', 'reusable' => true]);
        $this->hasMany('id', 'FormRequestsFields', 'id_form', ['alias' => 'FormRequestsFields', 'reusable' => true]);
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni', 'reusable' => true]);
        $this->belongsTo('id_tipologia_form', '\TipologieForm', 'id', ['alias' => 'TipologieForm', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoForm', 'id', ['alias' => 'TipologieStatoForm', 'reusable' => true]);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
        $this->allowEmptyStringValues(['email_to', 'email_bcc', 'email_cc', 'testo']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forms';
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return [
            'id' => 'id',
            'id_applicazione' => 'id_applicazione',
            'id_tipologia_form' => 'id_tipologia_form',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'titolo' => 'titolo',
            'testo' => 'testo',
            'key' => 'key',
            'email_to' => 'email_to',
            'email_cc' => 'email_cc',
            'email_bcc' => 'email_bcc',
            'invio_utente' => 'invio_utente',
            'submit_label' => 'submit_label',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'id_utente' => 'id_utente',
            'attivo' => 'attivo'
        ];
    }

}
