<?php

class TipologiePost extends BaseModel
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
     * @Column(type="string", length=175, nullable=false)
     */
    public $descrizione;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $slug;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $admin_menu;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $admin_icon;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $ordine;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return TipologiePost[]|TipologiePost
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return TipologiePost
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
    	parent::initialize ();

        $this->hasMany('id', 'Posts', 'id_tipologia_post', ['alias' => 'Posts', 'reusable' => true]);
        $this->allowEmptyStringValues(['admin_icon']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tipologie_post';
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
            'descrizione' => 'descrizione',
            'slug' => 'slug',
            'admin_menu' => 'admin_menu',
            'admin_icon' => 'admin_icon',
            'ordine' => 'ordine',
            'attivo' => 'attivo'
        ];
    }

    /*public function afterSave(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterEditPostEntity', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterEditPostEntity', $this);
    }

    public function afterCreate(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterCreatePostEntity', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterCreatePostEntity', $this);
    }

    public function beforeDelete(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterDeletePostEntity', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterDeletePostEntity', $this);
    }*/

    public function afterSave(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach( 'dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterEditAttribute', [$this->toArray()]);

        $eventsManager->attach( 'dispatch:afterEditPostType', new \apps\admin\plugins\FrontendGeneratorPlugin() );
        $eventsManager->fire('dispatch:afterEditPostType', $this);
    }

    public function afterCreate(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterEditAttribute', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterEditAttribute', [$this->toArray()]);

        $eventsManager->attach( 'dispatch:afterCreatePostType', new \apps\admin\plugins\FrontendGeneratorPlugin() );
        $eventsManager->fire('dispatch:afterCreatePostType', $this);
    }

    public function beforeDelete(){
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach ('dispatch:afterDeletePostEntity', new \apps\admin\plugins\FlatTablesManagerPlugin() );
        $eventsManager->fire('dispatch:afterDeletePostEntity', $this);

        $eventsManager->attach( 'dispatch:afterDeletePostType', new \apps\admin\plugins\FrontendGeneratorPlugin() );
        $eventsManager->fire('dispatch:afterDeletePostType', $this);
    }


}
