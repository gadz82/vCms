<?php

class TipologieUser extends BaseModel
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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $ordine;

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
     * @return TipologieStatoUtente[]
     */
    public static function find($parameters = null) {
        $key = 'tipologie_user_find.' . md5 ( json_encode ( $parameters ) );
        $rs = apcu_fetch ( $key );
        if (! $rs) {
            $rs = parent::find ( $parameters );
            apcu_store ( $key, $rs );
        }
        return $rs;
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RuoliPermessi
     */
    public static function findFirst($parameters = null) {
        $key = 'tipologie_user_find_first.' . md5 ( json_encode ( $parameters ) );
        $rs = apcu_fetch ( $key );
        if (! $rs) {
            $rs = parent::findFirst ( $parameters );
            apcu_store ( $key, $rs );
        }
        return $rs;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tipologie_user';
        $this->hasMany ( 'id', 'Users', 'id_tipologia_user', array (
            'alias' => 'Users',
            'reusable' => true
        ) );
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
            'ordine' => 'ordine',
            'attivo' => 'attivo'
        ];
    }

}
