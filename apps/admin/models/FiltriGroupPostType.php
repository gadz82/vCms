<?php

class FiltriGroupPostType extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=6, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_post;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_filtri_group;

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
     * @return FiltriGroupPostType[]|FiltriGroupPostType
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FiltriGroupPostType
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

        $this->belongsTo('id_filtri_group', '\FiltriGroup', 'id', ['alias' => 'FiltriGroup', 'reusable' => true]);
        $this->belongsTo('id_tipologia_post', '\TipologiePost', 'id', ['alias' => 'TipologiePost', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'filtri_group_post_type';
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
            'id_tipologia_post' => 'id_tipologia_post',
            'id_filtri_group' => 'id_filtri_group',
            'attivo' => 'attivo'
        ];
    }

    public function afterSave(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

    private function getTipologiePost(){
        return TipologiePost::query()
            ->innerJoin('FiltriGroupPostType', 'fgpt.id_tipologia_post = TipologiePost.id AND fgpt.attivo = 1', 'fgpt')
            ->where('fgpt.id = '.$this->id.' AND TipologiePost.attivo = 1')
            ->groupBy('TipologiePost.id')
            ->execute()->toArray();
    }

    public function afterCreate(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

    public function beforeDelete(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }

}
