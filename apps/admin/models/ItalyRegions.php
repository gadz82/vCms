<?php

class ItalyRegions extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=6, nullable=false)
     */
    public $id_regione;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $regione;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $descrizione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $superficie;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $num_residenti;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $num_comuni;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $num_provincie;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    public $presidente;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=true)
     */
    public $cod_istat;

    /**
     *
     * @var string
     * @Column(type="string", length=11, nullable=true)
     */
    public $cod_fiscale;

    /**
     *
     * @var string
     * @Column(type="string", length=11, nullable=true)
     */
    public $piva;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $pec;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $sito;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $sede;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItalyRegions[]|ItalyRegions
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItalyRegions
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
        $this->hasOne('id_regione_italy', 'Regioni', 'id_regione', ['alias' => 'Regioni', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'italy_regions';
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
            'id_regione' => 'id_regione',
            'regione' => 'regione',
            'descrizione' => 'descrizione',
            'superficie' => 'superficie',
            'num_residenti' => 'num_residenti',
            'num_comuni' => 'num_comuni',
            'num_provincie' => 'num_provincie',
            'presidente' => 'presidente',
            'cod_istat' => 'cod_istat',
            'cod_fiscale' => 'cod_fiscale',
            'piva' => 'piva',
            'pec' => 'pec',
            'sito' => 'sito',
            'sede' => 'sede'
        ];
    }

}
