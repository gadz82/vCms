<?php

class Applicazioni extends \BaseModel
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
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_applicazione;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $titolo;
    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    public $codice;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $descrizione;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $href_lang;

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
    public $id_utente_admin;

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
     * @return Applicazioni[]|Applicazioni
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Applicazioni
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
        $this->belongsTo('id_tipologia_applicazione', 'TipologieApplicazione', 'id', ['alias' => 'TipologieApplicazione', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', 'TipologieStatoApplicazione', 'id', ['alias' => 'TipologieStatoApplicazione', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'applicazioni';
    }


}
