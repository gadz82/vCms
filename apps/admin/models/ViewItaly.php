<?php

class ViewItaly extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $istat;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $comune;

    /**
     *
     * @var string
     * @Column(type="string", length=11, nullable=true)
     */
    public $cap;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $regione;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=true)
     */
    public $provincia;

    /**
     *
     * @var string
     * @Column(type="string", length=7, nullable=true)
     */
    public $prefisso;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=true)
     */
    public $cod_fisco;

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
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $lng;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $lat;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $abitanti;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $patrono_nome;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $patrono_data;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $indirizzo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ViewItaly[]|ViewItaly
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ViewItaly
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
        $this->setSchema("c0gustour");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'view_italy';
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
            'istat'         => 'istat',
            'comune'        => 'comune',
            'cap'           => 'cap',
            'regione'       => 'regione',
            'provincia'     => 'provincia',
            'prefisso'      => 'prefisso',
            'cod_fisco'     => 'cod_fisco',
            'superficie'    => 'superficie',
            'num_residenti' => 'num_residenti',
            'lng'           => 'lng',
            'lat'           => 'lat',
            'abitanti'      => 'abitanti',
            'patrono_nome'  => 'patrono_nome',
            'patrono_data'  => 'patrono_data',
            'indirizzo'     => 'indirizzo'
        ];
    }

}
