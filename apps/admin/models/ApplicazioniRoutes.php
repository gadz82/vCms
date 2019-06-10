<?php

class ApplicazioniRoutes extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=5, nullable=false)
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
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_route;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $nome;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $params;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=false)
     */
    public $ordine;

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
     * @return ApplicazioniRoutes[]|ApplicazioniRoutes
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ApplicazioniRoutes
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
        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoApplicazioneRoute', 'id', ['alias' => 'TipologieStatoApplicazioneRoute', 'reusable' => true]);
        $this->belongsTo('id_tipologia_route', '\TipologieRoutes', 'id', ['alias' => 'TipologieRoutes', 'reusable' => true]);
    }

    public function validation(){
        $validator = new Phalcon\Validation();
        $validator->add(
            'params',
            new \apps\admin\library\validation\JsonValidation()
        );
        return $this->validate($validator);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'applicazioni_routes';
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
            'id_tipologia_stato' => 'id_tipologia_stato',
            'id_tipologia_route' => 'id_tipologia_route',
            'nome' => 'nome',
            'path' => 'path',
            'params' => 'params',
            'ordine' => 'ordine',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
