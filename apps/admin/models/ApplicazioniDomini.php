<?php
use Phalcon\Validation;
use Phalcon\Validation\Validator\Url as UrlValidator;

class ApplicazioniDomini extends \BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=4, nullable=false)
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
     * @Column(type="string", length=150, nullable=false)
     */
    public $referer;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $ip_autorizzati;

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
     * @return ApplicazioniDomini[]|ApplicazioniDomini
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ApplicazioniDomini
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

        $this->belongsTo('id_applicazione', '\Applicazioni', 'id', ['alias' => 'Applicazioni']);
        $this->allowEmptyStringValues(array('referer'));
    }

    public function validation()
    {
        $validator = new Validation();
        $validator->add('referer', new UrlValidator([
            'message' => ':field must be a url'
        ]));
        return $this->validate($validator);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'applicazioni_domini';
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
            'referer' => 'referer',
            'ip_autorizzati' => 'ip_autorizzati',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
