<?php

class MetaGroup extends BaseModel
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
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $descrizione;

    /**
     *
     * @var string
     * @Column(type="int", length=3, nullable=false, default=10)
     */
    public $priorita;

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
     * @Column(type="integer", length=4, nullable=false)
     */
    public $system;

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
     * @return PostsMetaGroup[]|PostsMetaGroup
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PostsMetaGroup
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

        $this->hasMany('id', 'Meta', 'id_meta_group', ['alias' => 'Meta', 'reusable' => true]);
        $this->hasMany('id', 'MetaGroupPostType', 'id_meta_group', ['alias' => 'MetaGroupPostType', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'meta_group';
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
            'id'                 => 'id',
            'descrizione'        => 'descrizione',
            'priorita'           => 'priorita',
            'data_creazione'     => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'id_utente'          => 'id_utente',
            'system'             => 'system',
            'attivo'             => 'attivo'
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
            ->innerJoin('MetaGroupPostType', 'mgpt.id_tipologia_post = TipologiePost.id AND mgpt.attivo = 1', 'mgpt')
            ->where('mgpt.id_meta_group = ' . $this->id . ' AND TipologiePost.attivo = 1')
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
