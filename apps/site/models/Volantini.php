<?php

class Volantini extends BaseModel
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
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_volantino;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_punto_vendita;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_regione;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $id_punto_vendita;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=false)
     */
    public $titolo;

    /**
     *
     * @var date
     * @Column(type="year", length=4, nullable=false)
     */
    public $anno;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $numero;


    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_inizio_pubblicazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_fine_pubblicazione;

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
     * @return Volantini[]|Volantini
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Volantini
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function trashImages(Volantini $volantino)
    {
        $upload_path = self::getVolantinoPath($volantino);
        $upload_dir = BASE_DIR . '/../public/raw/volantini/' . $upload_path;
        if (!file_exists($upload_dir)) return false;

        $files = glob($upload_dir . '{,.}*', GLOB_BRACE); // get all file names

        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            } // delete file
        }
        return true;
    }

    public static function getVolantinoPath(Volantini $volantino)
    {
        $upload_path = $volantino->id . DIRECTORY_SEPARATOR;
        return $upload_path;

    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoVolantino', 'id', ['alias' => 'TipologieStatoVolantino', 'reusable' => true]);
        $this->belongsTo('id_tipologia_volantino', '\TipologieVolantino', 'id', ['alias' => 'TipologieVolantino', 'reusable' => true]);
        $this->belongsTo('id_tipologia_punto_vendita', '\TipologiePuntoVendita', 'id', ['alias' => 'TipologiePuntoVendita', 'reusable' => true]);
        $this->belongsTo('id_regione', '\Regioni', 'id', ['alias' => 'Regioni', 'reusable' => true]);
        $this->belongsTo('id_punto_vendita', '\PuntiVendita', 'id', ['alias' => 'PuntiVendita', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'volantini';
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
            'id'                         => 'id',
            'id_tipologia_stato'         => 'id_tipologia_stato',
            'id_tipologia_volantino'     => 'id_tipologia_volantino',
            'id_tipologia_punto_vendita' => 'id_tipologia_punto_vendita',
            'id_regione'                 => 'id_regione',
            'id_punto_vendita'           => 'id_punto_vendita',
            'titolo'                     => 'titolo',
            'anno'                       => 'anno',
            'numero'                     => 'numero',
            'data_inizio_pubblicazione'  => 'data_inizio_pubblicazione',
            'data_fine_pubblicazione'    => 'data_fine_pubblicazione',
            'data_creazione'             => 'data_creazione',
            'data_aggiornamento'         => 'data_aggiornamento',
            'attivo'                     => 'attivo'
        ];
    }
}
