<?php

class NotificheCarEvents extends BaseModel
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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_user_car_event;

    /**
     *
     * @var integer
     * @Column(type="integer", length=3, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $data_invio;

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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return NotificheCarEvents[]|NotificheCarEvents
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NotificheCarEvents
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
        $this->hasMany('id', 'NotificheUsersCarEvents', 'id_notifica_car_events', ['alias' => 'NotificheUsersCarEvents']);
        $this->belongsTo('id_user_car_event', '\UsersCarEvents', 'id', ['alias' => 'UsersCarEvents']);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoNotifica', 'id', ['alias' => 'TipologieStatoNotifica']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'notifiche_car_events';
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
            'id_user_car_event' => 'id_user_car_event',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'data_invio' => 'data_invio',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
