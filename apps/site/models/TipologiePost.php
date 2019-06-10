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
        parent::initialize();
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

}
