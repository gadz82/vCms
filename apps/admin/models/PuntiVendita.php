<?php

class PuntiVendita extends BaseModel
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
    public $id_tipologia_punto_vendita;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_regione;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $comune;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $nome;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $data;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=false)
     */
    public $lat;

    /**
     *
     * @var double
     * @Column(type="double", length=11, nullable=false)
     */
    public $lng;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $address;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=true)
     */
    public $id_pdv;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=true)
     */
    public $coop;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_creazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
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
     * @return PuntiVendita[]|PuntiVendita
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PuntiVendita
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
        $this->hasMany('id', 'Volantini', 'id_punto_vendita', ['alias' => 'Volantini', 'reusable' => true]);
        $this->belongsTo('id_tipologia_punto_vendita', '\TipologiePuntoVendita', 'id', ['alias' => 'TipologiePuntoVendita']);
        $this->belongsTo('id_regione', '\Regioni', 'id', ['alias' => 'Regioni']);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoPuntoVendita', 'id', ['alias' => 'TipologieStatoPuntoVendita']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'punti_vendita';
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
            'id'                         => 'id',
            'id_tipologia_punto_vendita' => 'id_tipologia_punto_vendita',
            'id_tipologia_stato'         => 'id_tipologia_stato',
            'id_regione'                 => 'id_regione',
            'comune'                     => 'comune',
            'nome'                       => 'nome',
            'data'                       => 'data',
            'lat'                        => 'lat',
            'lng'                        => 'lng',
            'address'                    => 'address',
            'id_pdv'                     => 'id_pdv',
            'coop'                       => 'coop',
            'data_creazione'             => 'data_creazione',
            'data_aggiornamento'         => 'data_aggiornamento',
            'attivo'                     => 'attivo'
        ];
    }

}
