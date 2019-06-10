<?php

class Files extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=9, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $original_filename;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $filename;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $filetype;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $filesize;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $filepath;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $fileurl;

    /**
     *
     * @var string
     * @Column(type="integer", length=2, nullable=false)
     */
    public $priorita;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $alt;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $private;

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
     * @return PostsFiles[]|PostsFiles
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PostsFiles
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
        $this->hasMany('id', 'FilesUsersGroups', 'id_file', ['alias' => 'FilesUsersGroups', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', '\TipologieStatoFile', 'id', ['alias' => 'TipologieStatoFile', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'files';
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
            'id_tipologia_stato' => 'id_tipologia_stato',
            'original_filename' => 'original_filename',
            'filename' => 'filename',
            'filetype' => 'filetype',
            'filesize' => 'filesize',
            'filepath' => 'filepath',
            'fileurl' => 'fileurl',
            'priorita' => 'priorita',
            'alt' => 'alt',
            'private' => 'private',
            'data_creazione' => 'data_creazione',
            'data_aggiornamento' => 'data_aggiornamento',
            'attivo' => 'attivo'
        ];
    }

}
