<?php

class Gruppi extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $id_tipologia_gruppo;

    /**
     *
     * @var string
     */
    public $descrizione;

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
     * @return Gruppi[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Gruppi
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

        $this->hasMany('id', 'GruppiUtenti', 'id_gruppo', [
            'alias'    => 'GruppiUtenti',
            'reusable' => true
        ]);
        $this->belongsTo('id_tipologia_gruppo', 'TipologieGruppo', 'id', [
            'alias'    => 'TipologieGruppo',
            'reusable' => true
        ]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'gruppi';
    }
}
