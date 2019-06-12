<?php

class FiltriGroup extends BaseModel
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
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return FiltriGroup[]|FiltriGroup
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FiltriGroup
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

        $this->hasMany('id', 'Filtri', 'id_filtri_group', ['alias' => 'Filtri', 'reusable' => true]);
        $this->hasMany('id', 'FiltriGroupPostType', 'id_filtri_group', ['alias' => 'FiltriGroupPostType', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'filtri_group';
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
            ->innerJoin('FiltriGroupPostType', 'fgpt.id_tipologia_post = TipologiePost.id AND fgpt.attivo = 1', 'fgpt')
            ->where('fgpt.id_filtri_group = ' . $this->id . ' AND TipologiePost.attivo = 1')
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
