<?php

class BlocksHistory extends \Phalcon\Mvc\Model
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
    public $id_block;

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
     * @Column(type="string", nullable=true)
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
     * @return BlocksHistory[]|BlocksHistory
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BlocksHistory
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
        $this->setSchema(\Phalcon\Di::getDefault()->get('config')->database->dbname);
        $this->belongsTo('id_block_tag', '\BlocksTags', 'id', ['alias' => 'BlocksTags']);
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni']);
        $this->belongsTo('id_tipologia_block', '\TipologieBlock', 'id', ['alias' => 'TipologieBlock']);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoBlock', 'id', ['alias' => 'TipologieStatoBlock']);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti']);
        $this->belongsTo('id_block', '\Blocks', 'id', ['alias' => 'Blocks']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'blocks_history';
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
            'id'                        => 'id',
            'id_block'                  => 'id_block',
            'id_applicazione'           => 'id_applicazione',
            'id_tipologia_stato'        => 'id_tipologia_stato',
            'id_tipologia_block'        => 'id_tipologia_block',
            'id_block_tag'              => 'id_block_tag',
            'titolo'                    => 'titolo',
            'key'                       => 'key',
            'content'                   => 'content',
            'data_creazione'            => 'data_creazione',
            'data_aggiornamento'        => 'data_aggiornamento',
            'data_inizio_pubblicazione' => 'data_inizio_pubblicazione',
            'data_fine_pubblicazione'   => 'data_fine_pubblicazione',
            'id_utente'                 => 'id_utente',
            'attivo'                    => 'attivo'
        ];
    }

}
