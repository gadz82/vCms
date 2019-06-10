<?php
class GruppiUtenti extends BaseModel {
	
	/**
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 *
	 * @var integer
	 */
	public $id_gruppo;
	
	/**
	 *
	 * @var integer
	 */
	public $id_utente;
	
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
	 * @return GruppiUtenti[]
	 */
	public static function find($parameters = null) {
		return parent::find ( $parameters );
	}
	
	/**
	 * Allows to query the first record that match the specified conditions
	 *
	 * @param mixed $parameters
	 * @return GruppiUtenti
	 */
	public static function findFirst($parameters = null) {
		return parent::findFirst ( $parameters );
	}
	
	/**
	 * Initialize method for model.
	 */
	public function initialize() {
		parent::initialize ();

		$this->belongsTo ( 'id_gruppo', 'Gruppi', 'id', array (
				'alias' => 'Gruppi',
				'reusable' => true
		) );
		$this->belongsTo ( 'id_utente', 'Utenti', 'id', array (
				'alias' => 'Utenti',
				'reusable' => true
		) );
	}
	
	/**
	 * Returns table name mapped in the model.
	 *
	 * @return string
	 */
	public function getSource() {
		return 'gruppi_utenti';
	}
}
