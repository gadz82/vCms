<?php

class Posts extends BaseModel
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
    public $id_tipologia_post;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=true)
     */
    public $slug;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $excerpt;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $testo;

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
     * @return Posts[]|Posts
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Posts
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
        $this->hasMany('id', 'PostsMeta', 'post_id', ['alias' => 'PostsMeta', 'reusable' => true]);
        $this->hasMany('id', 'PostsTags', 'id_post', ['alias' => 'PostsTags', 'reusable' => true]);
        $this->hasMany('id', 'PostsFiles', 'id_post', ['alias' => 'PostsFiles', 'reusable' => true]);
        $this->hasMany('id', 'PostsFiltri', 'id_post', ['alias' => 'PostsFiltri', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoPost', 'id', ['alias' => 'TipologieStatoPost', 'reusable' => true]);
        $this->belongsTo('id_tipologia_post', '\TipologiePost', 'id', ['alias' => 'TipologiePost', 'reusable' => true]);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni', 'reusable' => true]);
        $this->allowEmptyStringValues(['data_inizio_pubblicazione', 'data_fine_pubblicazione', 'excerpt']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'posts';
    }

    public function beforeCreate()
    {
        if ($this->data_inizio_pubblicazione == $this->data_fine_pubblicazione || empty($this->data_fine_pubblicazione)) {
            $this->data_fine_pubblicazione = new \Phalcon\Db\RawValue('NULL');
        }
    }

    public function beforeUpdate()
    {
        if ($this->data_inizio_pubblicazione == $this->data_fine_pubblicazione || empty($this->data_fine_pubblicazione)) {
            $this->data_fine_pubblicazione = new \Phalcon\Db\RawValue('NULL');
        }
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
            'id_tipologia_post' => 'id_tipologia_post',
            'titolo' => 'titolo',
            'slug' => 'slug',
            'excerpt' => 'excerpt',
            'testo' => 'testo',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'data_inizio_pubblicazione' => 'data_inizio_pubblicazione',
            'data_fine_pubblicazione' => 'data_fine_pubblicazione',
            'id_utente' => 'id_utente',
            'attivo' => 'attivo'
        ];
    }
}
