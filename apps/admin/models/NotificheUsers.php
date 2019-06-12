<?php

class NotificheUsers extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_notifica;

    /**
     *
     * @var integer
     * @Column(type="integer", length=6, nullable=false)
     */
    public $id_user_device;

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
    public $data_schedulazione;

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
     * @return NotificheUsers[]|NotificheUsers
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NotificheUsers
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
        $this->belongsTo('id_notifica', '\Notifiche', 'id', ['alias' => 'Notifiche', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoNotificaUsers', 'id', ['alias' => 'TipologieStatoNotificaUsers', 'reusable' => true]);
        $this->belongsTo('id_user_device', '\UsersDevices', 'id', ['alias' => 'UsersDevices', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'notifiche_users';
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
            'id'                 => 'id',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'id_notifica'        => 'id_notifica',
            'id_user_device'     => 'id_user_device',
            'data_invio'         => 'data_invio',
            'data_schedulazione' => 'data_schedulazione',
            'data_creazione'     => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo'             => 'attivo'
        ];
    }

}
