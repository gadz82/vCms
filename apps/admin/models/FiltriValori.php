<?php

class FiltriValori extends BaseModel
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
     * @Column(type="integer", length=10, nullable=false)
     */
    public $id_filtro;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $id_filtro_valore_parent;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $valore;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $key;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $meta_title;

    /**
     *
     * @var string
     * @Column(type="string", length=275, nullable=false)
     */
    public $meta_description;

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
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return FiltriValori[]|FiltriValori
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FiltriValori
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

        $this->hasMany('id', 'PostsFiltri', 'id_filtro_valore', ['alias' => 'PostsFiltri', 'reusable' => true]);
        $this->belongsTo('id_filtro', '\Filtri', 'id', ['alias' => 'Filtri', 'reusable' => true]);
        $this->belongsTo('id_filtro_valore_parent', '\FiltriValori', 'id', [
            'alias'      => 'FiltroValoreParent',
            'reusable'   => true,
            'foreignKey' => [
                'allowNulls' => true
            ],
            'reusable'   => true
        ]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'filtri_valori';
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
            'id'                      => 'id',
            'id_filtro'               => 'id_filtro',
            'id_filtro_valore_parent' => 'id_filtro_valore_parent',
            'valore'                  => 'valore',
            'key'                     => 'key',
            'meta_title'              => 'meta_title',
            'meta_description'        => 'meta_description',
            'data_creazione'          => 'data_creazione',
            'data_aggiornamento'      => 'data_aggiornamento',
            'attivo'                  => 'attivo'
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
            ->innerJoin('Filtri', 'f.id_filtri_group = fg.id AND f.attivo = 1', 'f')
            ->where('f.id = ' . $this->id_filtro . ' AND TipologiePost.attivo = 1')
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
