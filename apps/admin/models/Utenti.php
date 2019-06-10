<?php
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
class Utenti extends BaseModel {
	
	/**
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 *
	 * @var integer
	 */
	public $id_tipologia_utente;
	
	/**
	 *
	 * @var integer
	 */
	public $id_tipologia_stato;
	
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
	public $nome_utente;
	
	/**
	 *
	 * @var string
	 */
	public $password;
	
	/**
	 *
	 * @var string
	 */
	public $nome;
	
	/**
	 *
	 * @var string
	 */
	public $cognome;
	
	/**
	 *
	 * @var string
	 */
	public $email;
	
	/**
	 *
	 * @var string
	 */
	public $avatar;
	
	/**
	 *
	 * @var string
	 */
	public $token;
	/**
	 *
	 * @var string
	 */
	public $api_level;
	/**
	 *
	 * @var string
	 */
	public $public_key;
	/**
	 *
	 * @var string
	 */
	public $private_key;
	
	/**
	 *
	 * @var string
	 */
	public $data_creazione_token;
	
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
	 * @return Utenti[]
	 */
	public static function find($parameters = null) {
		return parent::find ( $parameters );
	}
	
	/**
	 * Allows to query the first record that match the specified conditions
	 *
	 * @param mixed $parameters
	 * @return Utenti
	 */
	public static function findFirst($parameters = null) {
		return parent::findFirst ( $parameters );
	}
	
	/**
	 * Validations and business logic
	 *
	 * @return boolean
	 */
	public function validation() {
		$validator = new Validation();
		$validator->add('email', new Email( array (
				'field' => 'email',
				'required' => true
		) ));

		return $this->validate($validator);

		return true;
	}
	
	/**
	 * Initialize method for model.
	 */
	public function initialize() {
		parent::initialize ();

		$this->hasMany ( 'id', 'GruppiUtenti', 'id_utente', array (
				'alias' => 'GruppiUtenti'
		) );

		$this->belongsTo ( 'id_ruolo', 'Ruoli', 'id', array (
				'alias' => 'Ruoli'
		) );
		$this->belongsTo ( 'id_tipologia_stato', 'TipologieStatoUtente', 'id', array (
				'alias' => 'TipologieStatoUtente'
		) );
		$this->belongsTo ( 'id_tipologia_utente', 'TipologieUtente', 'id', array (
				'alias' => 'TipologieUtente'
		) );
	}
	
	/**
	 * Returns table name mapped in the model.
	 *
	 * @return string
	 */
	public function getSource() {
		return 'utenti';
	}
}
