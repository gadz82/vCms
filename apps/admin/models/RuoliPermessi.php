<?php

class RuoliPermessi extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $id_ruolo;

    /**
     *
     * @var integer
     */
    public $livello;

    /**
     *
     * @var string
     */
    public $risorsa;

    /**
     *
     * @var string
     */
    public $azione;

    /**
     *
     * @var string
     */
    public $data_creazione;

    /**
     *
     * @var string
     */
    public $data_aggiornamento;

    /**
     *
     * @var integer
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RuoliPermessi[]
     */
    public static function find($parameters = null)
    {
        $key = 'ruoli_permessi_find.' . md5(json_encode($parameters));
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
        $key = 'ruoli_permessi_find_first.' . md5(json_encode($parameters));
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

        $this->belongsTo('id_ruolo', 'Ruoli', 'id', [
            'alias'    => 'Ruoli',
            'reusable' => true
        ]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'ruoli_permessi';
    }
}
