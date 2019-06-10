<?php

class Blocks extends BaseModel
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
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_block;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_block_tag;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $key;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $content;

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
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $data_inizio_pubblicazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $data_fine_pubblicazione;

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
     * @return Blocks[]|Blocks
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Blocks
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
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni', 'reusable' => true]);
        $this->belongsTo('id_tipologia_block', '\TipologieBlock', 'id', ['alias' => 'TipologieBlock', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoBlock', 'id', ['alias' => 'TipologieStatoBlock', 'reusable' => true]);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'blocks';
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
            'id_tipologia_stato' => 'id_tipologia_stato',
            'id_tipologia_block' => 'id_tipologia_block',
            'id_block_tag' => 'id_block_tag',
            'titolo' => 'titolo',
            'key' => 'key',
            'content' => 'content',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'data_inizio_pubblicazione' => 'data_inizio_pubblicazione',
            'data_fine_pubblicazione' => 'data_fine_pubblicazione',
            'id_utente' => 'id_utente',
            'attivo' => 'attivo'
        ];
    }

}
