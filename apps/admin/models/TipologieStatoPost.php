<?php

class TipologieStatoPost extends BaseModel
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
     * @return TipologieStatoPost[]|TipologieStatoPost
     */
    public static function find($parameters = null)
    {
        $key = 'tipologie_stato_post_find.' . md5(json_encode($parameters));
        $rs = apcu_fetch($key);
        if (!$rs) {
            $rs = parent::find($parameters);
            apcu_store($key, $rs);
        }
        return $rs;
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RuoliPermessi
     */
    public static function findFirst($parameters = null)
    {
        $key = 'tipologie_stato_post_find_first.' . md5(json_encode($parameters));
        $rs = apcu_fetch($key);
        if (!$rs) {
            $rs = parent::findFirst($parameters);
            apcu_store($key, $rs);
        }
        return $rs;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->hasMany('id', 'Posts', 'id_tipologia_stato', ['alias' => 'Posts', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tipologie_stato_post';
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
            'id'          => 'id',
            'descrizione' => 'descrizione',
            'ordine'      => 'ordine',
            'attivo'      => 'attivo'
        ];
    }

}
