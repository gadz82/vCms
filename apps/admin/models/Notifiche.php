<?php

class Notifiche  extends BaseModel {

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
     * @Column(type="integer", length=3, nullable=false)
     */
    public $id_tipologia_notifica;

    /**
     *
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=false)
     */
    public $testo;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $navTo;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $navToParams;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $data_invio;

    public $ids_destinatari;

    public $notifica_push;

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
     * @Column(type="integer", length=1, nullable=true)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Notifiche[]|Notifiche
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Notifiche
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
        $this->hasMany('id', 'NotificheUsers', 'id_notifica', ['alias' => 'NotificheUsers', 'reusable' => true]);
        $this->belongsTo('id_tipologia_notifica', '\TipologieNotifica', 'id', ['alias' => 'TipologieNotifica', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoNotifica', 'id', ['alias' => 'TipologieStatoNotifica', 'reusable' => true]);
        $this->allowEmptyStringValues(['navTo', 'navToParams']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'notifiche';
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
            'id_tipologia_notifica' => 'id_tipologia_notifica',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'titolo' => 'titolo',
            'testo' => 'testo',
            'navTo' => 'navTo',
            'navToParams' => 'navToParams',
            'data_invio' => 'data_invio',
            'notifica_push' => 'notifica_push',
            'ids_destinatari' => 'ids_destinatari',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
