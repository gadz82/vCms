<?php

class Tags extends BaseModel
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
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $tag;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $content;

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
     * @return Tags[]|Tags
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tags
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
        $this->hasMany('id', 'PostsTags', 'id_tag', ['alias' => 'PostsTags']);
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni']);
        $this->allowEmptyStringValues(['content']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tags';
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
            'titolo' => 'titolo',
            'tag' => 'tag',
            'content' => 'content',
            'attivo' => 'attivo'
        ];
    }

}
