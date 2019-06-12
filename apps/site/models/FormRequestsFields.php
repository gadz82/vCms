<?php

class FormRequestsFields extends BaseModel
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
    public $id_form_request;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_form;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_form_field;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $input_value;

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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return FormRequestsFields[]|FormRequestsFields
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FormRequestsFields
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
        $this->belongsTo('id_form_field', '\FormFields', 'id', ['alias' => 'FormFields']);
        $this->belongsTo('id_form_request', '\FormRequests', 'id', ['alias' => 'FormRequests']);
        $this->belongsTo('id_form', '\Forms', 'id', ['alias' => 'Forms']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'form_requests_fields';
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
            'id'                 => 'id',
            'id_form_request'    => 'id_form_request',
            'id_form'            => 'id_form',
            'id_form_field'      => 'id_form_field',
            'input_value'        => 'input_value',
            'data_creazione'     => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo'             => 'attivo'
        ];
    }

}
