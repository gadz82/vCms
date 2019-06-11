<?php

class Meta extends BaseModel
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
    public $id_meta_group;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_meta;

    /**
     *
     * @var string
     * @Column(type="string", length=175, nullable=false)
     */
    public $key;

    /**
     *
     * @var string
     * @Column(type="string", length=175, nullable=false)
     */
    public $label;

    /**
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
     */
    public $priorita;

    /**
     *
     * @var string
     * @Column(type="text", nullable=true)
     */
    public $dataset;

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
    public $hidden;

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
     * @return Meta[]|Meta
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Meta
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
        $this->hasMany('id', 'PostsMeta ', 'id_meta', ['alias' => 'PostsMeta', 'reusable' => true]);
        $this->belongsTo('id_meta_group', '\MetaGroup', 'id', ['alias' => 'MetaGroup', 'reusable' => true]);
        $this->belongsTo('id_tipologia_meta', '\TipologieMeta', 'id', ['alias' => 'TipologieMeta', 'reusable' => true]);
        $this->allowEmptyStringValues(array('dataset'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'meta';
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
            'id_meta_group' => 'id_meta_group',
            'id_tipologia_meta' => 'id_tipologia_meta',
            'key' => 'key',
            'label' => 'label',
            'dataset' => 'dataset',
            'required' => 'required',
            'autoload' => 'autoload',
            'priorita' => 'priorita',
            'hidden' => 'hidden',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'id_utente' => 'id_utente',
            'attivo' => 'attivo'
        ];
    }

}
