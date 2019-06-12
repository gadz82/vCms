<?php

class Regioni extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $descrizione;

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
     * @return Regioni[]|Regioni
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Regioni
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
        $this->belongsTo('id_regione', '\ItalyRegions', 'id_regione_italy', ['alias' => 'ItalyRegions', 'reusable' => true]);
        $this->hasMany('id', 'PuntiVendita', 'id_regione', ['alias' => 'PuntiVendita', 'reusable' => true]);
        $this->hasMany('id', 'Volantini', 'id_regione', ['alias' => 'Volantini', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'regioni_pac';
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
            'id'               => 'id',
            'id_regione_italy' => 'id_regione_italy',
            'descrizione'      => 'descrizione',
            'attivo'           => 'attivo'
        ];
    }

}
