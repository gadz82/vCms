<?php

class TipologieGruppo extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $descrizione;

    /**
     *
     * @var string
     */
    public $vincoli_controller;

    /**
     *
     * @var integer
     */
    public $ordine;

    /**
     *
     * @var string
     */
    public $data_creazione;

    /**
     *
     * @var string
     */
    public $data_aggiornamento;

    /**
     *
     * @var integer
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return TipologieGruppo[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return TipologieGruppo
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

        $this->hasMany('id', 'Gruppi', 'id_tipologia_gruppo', [
            'alias' => 'Gruppi', 'reusable' => true
        ]);

        $this->allowEmptyStringValues([
            'vincoli_model'
        ]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tipologie_gruppo';
    }

    public function afterFetch()
    {
        $this->vincoli_controller = !empty ($this->vincoli_controller) ? json_decode($this->vincoli_controller, true) : [];
    }
}
