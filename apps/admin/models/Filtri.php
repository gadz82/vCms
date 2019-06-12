<?php

class Filtri extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    public $id_applicazione;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_filtri_group;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_filtro;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_filtro_parent;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $key;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $one_to_one;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $required;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $frontend_filter;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $descrizione;

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
     * @return Filtri[]|Filtri
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Filtri
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

        $this->hasMany('id', 'FiltriValori', 'id_filtro', ['alias' => 'FiltriValori', 'reusable' => true]);
        $this->hasMany('id', 'PostsFiltri', 'id_filtro', ['alias' => 'PostsFiltri', 'reusable' => true]);
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni', 'reusable' => true]);
        $this->belongsTo('id_filtri_group', '\FiltriGroup', 'id', ['alias' => 'FiltriGroup', 'reusable' => true]);
        $this->belongsTo('id_filtro_parent', '\Filtri', 'id', [
            'alias'      => 'FiltroParent',
            'reusable'   => true,
            'foreignKey' => [
                'allowNulls' => true
            ]
        ]);
        $this->belongsTo('id_tipologia_filtro', '\TipologieFiltro', 'id', ['alias' => 'TipologieFiltro', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoFiltro', 'id', ['alias' => 'TipologieStatoFiltro', 'reusable' => true]);
        $this->belongsTo('id_utente', '\Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
        $this->allowEmptyStringValues(['descrizione']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'filtri';
    }

    public function setIdFiltroParent($value)
    {
        if (empty($value)) {
            $this->id_filtro_parent = null;
        } else {
            $this->id_filtro_parent = $value;
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
            'id'                  => 'id',
            'id_applicazione'     => 'id_applicazione',
            'id_filtri_group'     => 'id_filtri_group',
            'id_tipologia_filtro' => 'id_tipologia_filtro',
            'id_tipologia_stato'  => 'id_tipologia_stato',
            'id_filtro_parent'    => 'id_filtro_parent',
            'one_to_one'          => 'one_to_one',
            'required'            => 'required',
            'frontend_filter'     => 'frontend_filter',
            'titolo'              => 'titolo',
            'key'                 => 'key',
            'descrizione'         => 'descrizione',
            'data_creazione'      => 'data_creazione',
            'data_aggiornamento'  => 'data_aggiornamento',
            'id_utente'           => 'id_utente',
            'attivo'              => 'attivo'
        ];
    }

    public function afterSave()
    {
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin());
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

    private function getTipologiePost()
    {
        return TipologiePost::query()
            ->innerJoin('FiltriGroupPostType', 'fgpt.id_tipologia_post = TipologiePost.id AND fgpt.attivo = 1', 'fgpt')
            ->innerJoin('FiltriGroup', 'fg.id = fgpt.id_filtri_group AND fg.attivo = 1', 'fg')
            ->where('fg.id = ' . $this->id_filtri_group . ' AND TipologiePost.attivo = 1')
            ->groupBy('TipologiePost.id')
            ->execute()->toArray();
    }

    public function afterCreate()
    {
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin());
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

    public function beforeDelete()
    {
        $eventsManager = new Phalcon\Events\Manager();
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

}
