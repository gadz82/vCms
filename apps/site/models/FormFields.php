<?php

class FormFields extends BaseModel
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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_form;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_form_fields;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $label;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $placeholder;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $value;

    /**
     *
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
     */
    public $ordine;

    /**
     *
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
     */
    public $obbligatorio;

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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return FormFields[]|FormFields
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FormFields
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
        $this->hasMany('id', 'FormRequestsFields', 'id_form_field', ['alias' => 'FormRequestsFields']);
        $this->belongsTo('id_form', '\Forms', 'id', ['alias' => 'Forms']);
        $this->belongsTo('id_tipologia_form_fields', '\TipologieFormFields', 'id', ['alias' => 'TipologieFormFields']);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoFormFields', 'id', ['alias' => 'TipologieStatoFormFields']);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'form_fields';
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
            'id_form' => 'id_form',
            'id_tipologia_form_fields' => 'id_tipologia_form_fields',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'name' => 'name',
            'label' => 'label',
            'placeholder' => 'placeholder',
            'value' => 'value',
            'obbligatorio' => 'obbligatorio',
            'ordine' => 'ordine',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'id_utente' => 'id_utente',
            'attivo' => 'attivo'
        ];
    }

}
