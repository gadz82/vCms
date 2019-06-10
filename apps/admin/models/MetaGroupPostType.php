<?php

class MetaGroupPostType extends BaseModel
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
    public $id_meta_group;

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
     * @return MetaGroupPostType[]|MetaGroupPostType
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MetaGroupPostType
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

        $this->belongsTo('id_meta_group', '\MetaGroup', 'id', ['alias' => 'MetaGroup', 'reusable' => true]);
        $this->belongsTo('id_tipologia_post', '\TipologiePost', 'id', ['alias' => 'TipologiePost', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'meta_group_post_type';
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
            'id_meta_group' => 'id_meta_group',
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
            ->innerJoin('MetaGroupPostType', 'mgpt.id_tipologia_post = TipologiePost.id AND mgpt.attivo = 1', 'mgpt')
            ->where('mgpt.id_meta_group = '.$this->id.' AND TipologiePost.attivo = 1')
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
        $tipologiePost = $this->getTipologiePost();
        $eventsManager->fire('dispatch:afterEditAttribute', $tipologiePost);
    }
}
