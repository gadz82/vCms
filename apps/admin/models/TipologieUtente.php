<?php
class TipologieUtente extends BaseModel {
	
	/**
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 *
	 * @var string
	 */
	public $descrizione;
	
	/**
	 *
	 * @var integer
	 */
	public $ordine;
	
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
	 * @return TipologieStatoUtente[]
	 */
	public static function find($parameters = null) {
		$key = 'tipologie_utente_find.' . md5 ( json_encode ( $parameters ) );
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
		$key = 'tipologie_utente_find_first.' . md5 ( json_encode ( $parameters ) );
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
	public function initialize() {
		parent::initialize ();

		$this->hasMany ( 'id', 'Utenti', 'id_tipologia_utente', array (
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
		return 'tipologie_utente';
	}
}
