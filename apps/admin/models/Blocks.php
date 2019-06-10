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
        $this->belongsTo('id_block_tag', '\BlocksTags', 'id', ['alias' => 'BlocksTags', 'reusable' => true]);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
        $this->hasMany('id', 'BlocksHistory', 'id_block', ['alias' => 'BlocksHistory', 'reusable' => true]);
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

    public function beforeUpdate(){
        $old = Blocks::findFirstById($this->id);

        $history = new BlocksHistory();
        $history->id_block = $this->id;
        $history->id_applicazione = $old->id_applicazione;
        $history->id_tipologia_stato = $old->id_tipologia_stato;
        $history->id_tipologia_block = $old->id_tipologia_block;
        $history->id_block_tag = $old->id_block_tag;
        $history->titolo = $old->titolo;
        $history->key = $old->key;
        $history->content = $old->content;
        $history->data_creazione = $old->data_creazione;
        $history->data_aggiornamento = (new \DateTime())->format('Y-m-d H:i:s');
        $history->data_inizio_pubblicazione = $old->data_inizio_pubblicazione;
        $history->data_fine_pubblicazione = $old->data_fine_pubblicazione;
        $history->id_utente = $old->id_utente;
        $history->attivo = $old->attivo;
        $history->save();
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
