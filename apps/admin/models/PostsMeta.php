<?php

class PostsMeta extends BaseModel
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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $post_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_post_meta;

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
    public $id_meta;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $meta_key;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $meta_value_int;

    /**
     *
     * @var decimal
     * @Column(type="decimal", length=12,6, nullable=true)
     */
    public $meta_value_decimal;

    /**
     *
     * @var string
     * @Column(type="string", length=175, nullable=true)
     */
    public $meta_value_varchar;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $meta_value_text;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $meta_value_datetime;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $meta_value_files;

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
     * @return PostsMeta[]|PostsMeta
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function getMetaValue(PostsMeta $postsMeta)
    {
        switch ($postsMeta->Meta->TipologieMeta->descrizione) {
            case "Intero":
                $meta_value = $postsMeta->meta_value_int;
                break;
            case "Decimale":
                $meta_value = $postsMeta->meta_value_decimal;
                break;
            case "Stringa":
                $meta_value = $postsMeta->meta_value_varchar;
                break;
            case "Testo":
                $meta_value = $postsMeta->meta_value_text;
                break;
            case "Date/Time":
                $meta_value = $postsMeta->meta_value_datetime;
                break;
            case "Select":
                $meta_value = $postsMeta->meta_value_varchar;
                break;
            case "Checkbox":
                $meta_value = $postsMeta->meta_value_int;
                break;
            case "File":
                $meta_value = $postsMeta->meta_value_files;
                break;
            case "File Collection":
                $meta_value = $postsMeta->meta_value_varchar;
                break;
            case "Html":
                $meta_value = $postsMeta->meta_value_text;
                break;
        }
        return $meta_value;
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PostsMeta
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

        $this->belongsTo('post_id', '\Posts', 'id', ['alias' => 'Posts', 'reusable' => true]);
        $this->belongsTo('id_tipologia_post_meta', '\TipologieMeta', 'id', ['alias' => 'TipologieMeta', 'reusable' => true]);
        $this->belongsTo('id_meta', '\Meta', 'id', ['alias' => 'Meta', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoPostMeta', 'id', ['alias' => 'TipologieStatoPostMeta', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'posts_meta';
    }

    public function beforeUpdate()
    {
        if (!$this->meta_value_files || empty($this->meta_value_files) || is_null($this->meta_value_files)) {
            $this->meta_value_files = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_datetime || empty($this->meta_value_datetime)) {
            $this->meta_value_datetime = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_varchar || empty($this->meta_value_varchar)) {
            $this->meta_value_varchar = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_text || empty($this->meta_value_text)) {
            $this->meta_value_text = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_decimal || empty($this->meta_value_decimal)) {
            $this->meta_value_decimal = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_int || empty($this->meta_value_int)) {
            $this->meta_value_int = new \Phalcon\Db\RawValue('NULL');
        }

    }

    public function beforeCreate()
    {
        if (!$this->meta_value_files || empty($this->meta_value_files) || is_null($this->meta_value_files)) {
            $this->meta_value_files = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_datetime || empty($this->meta_value_datetime)) {
            $this->meta_value_datetime = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_varchar || empty($this->meta_value_varchar)) {
            $this->meta_value_varchar = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_text || empty($this->meta_value_text)) {
            $this->meta_value_text = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_decimal || empty($this->meta_value_decimal)) {
            $this->meta_value_decimal = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_int || empty($this->meta_value_int)) {
            $this->meta_value_int = new \Phalcon\Db\RawValue('NULL');
        }
        if (!$this->meta_value_text || empty($this->meta_value_text)) {
            $this->meta_value_text = new \Phalcon\Db\RawValue('NULL');
        }
    }

    public function setMetaValue(PostsMeta $postMeta, $tipologia_meta, $valore)
    {
        switch ($tipologia_meta) {
            case "Intero":
                $postMeta->meta_value_int = (int)$valore;
                break;
            case "Decimale":
                $postMeta->meta_value_decimal = $valore;
                break;
            case "Stringa":
                $postMeta->meta_value_varchar = substr($valore, 0, 255);
                break;
            case "Testo":
                $postMeta->meta_value_text = $valore;
                break;
            case "Html":
                $postMeta->meta_value_text = $valore;
                break;
            case "Date/Time":
                $postMeta->meta_value_datetime = $valore;
                break;
            case "Select":
                $postMeta->meta_value_varchar = $valore;
                break;
            case "Checkbox":
                $postMeta->meta_value_int = (int)$valore;
                break;
            case "File":
                $postMeta->meta_value_files = $valore;
                break;
            case "File Collection":
                $postMeta->meta_value_varchar = $valore !== 'null' ? $valore : '';
                break;
        }
        return $postMeta;
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
            'id'                     => 'id',
            'post_id'                => 'post_id',
            'id_tipologia_post_meta' => 'id_tipologia_post_meta',
            'id_tipologia_stato'     => 'id_tipologia_stato',
            'id_meta'                => 'id_meta',
            'id_post_meta_group'     => 'id_post_meta_group',
            'meta_key'               => 'meta_key',
            'meta_value_int'         => 'meta_value_int',
            'meta_value_decimal'     => 'meta_value_decimal',
            'meta_value_varchar'     => 'meta_value_varchar',
            'meta_value_text'        => 'meta_value_text',
            'meta_value_datetime'    => 'meta_value_datetime',
            'meta_value_files'       => 'meta_value_files',
            'data_creazione'         => 'data_creazione',
            'data_aggiornamento'     => 'data_aggiornamento',
            'attivo'                 => 'attivo'
        ];
    }

}
