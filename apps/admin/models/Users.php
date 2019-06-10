<?php

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class Users extends BaseModel
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
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id_users_groups;

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
    public $id_tipologia_user;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=false)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=250, nullable=false)
     */
    public $nome;

    /**
     *
     * @var string
     * @Column(type="string", length=250, nullable=false)
     */
    public $cognome;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=true)
     */
    public $telefono;

    /**
     *
     * @var string
     * @Column(type="string", length=250, nullable=true)
     */
    public $indirizzo;

    /**
     *
     * @var string
     * @Column(type="string", length=125, nullable=true)
     */
    public $localita;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=true)
     */
    public $cap;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $data_di_nascita;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $validation_token;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $token_validated;

    /**
     *
     * @var string
     * @Column(type="string", length=32, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $password_reset_token;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $validation_expiration_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $reset_password_expiration_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $user_registration_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $user_last_login;

    /**
     *
     * @var string
     * @Column(type="string", length=25, nullable=true)
     */
    public $ip_address;

    /**
     *
     * @var string
     * @Column(type="string", length=250, nullable=true)
     */
    public $facebook_auth_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $facebook_auth_token;

    /**
     *
     * @var string
     * @Column(type="string", length=250, nullable=true)
     **/
    public $google_auth_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $google_auth_token;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_creazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
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
     * @return Users[]|Users
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model' => $this,
                    'message' => 'Inserisci una mail valida',
                ]));
        $validator->add(
            'email',
            new UniquenessValidator([
                'message' => 'Email in uso da un altro utente'
            ]));
        $validator->add(
            'username',
            new UniquenessValidator([
                'message' => 'Username in uso da un altro utente'
            ]));

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo('id_users_groups', '\UsersGroups', 'id', ['alias' => 'UsersGroups']);
        $this->belongsTo('id_tipologia_user', '\TipologieUser', 'id', ['alias' => 'TipologieUser']);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoUser', 'id', ['alias' => 'TipologieStatoUser']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
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
            'id_users_groups' => 'id_users_groups',
            'id_tipologia_stato' => 'id_tipologia_stato',
            'id_tipologia_user' => 'id_tipologia_user',
            'username' => 'username',
            'email' => 'email',
            'nome' => 'nome',
            'cognome' => 'cognome',
            'telefono' => 'telefono',
            'indirizzo' => 'indirizzo',
            'localita' => 'localita',
            'cap' => 'cap',
            'data_di_nascita' => 'data_di_nascita',
            'validation_token' => 'validation_token',
            'token_validated' => 'token_validated',
            'password' => 'password',
            'password_reset_token' => 'password_reset_token',
            'validation_expiration_date' => 'validation_expiration_date',
            'reset_password_expiration_date' => 'reset_password_expiration_date',
            'user_registration_date' => 'user_registration_date',
            'user_last_login' => 'user_last_login',
            'ip_address' => 'ip_address',
            'facebook_auth_id' => 'facebook_auth_id',
            'facebook_auth_token' => 'facebook_auth_token',
            'google_auth_id' => 'google_auth_id',
            'google_auth_token' => 'google_auth_token',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
