<?php

class FilesSizes extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $key;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $max_width;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $max_height;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $crop;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    public static function find($parameters = null) {
        $key = 'files_sizes_find.' . md5 ( json_encode ( $parameters ) );
        $rs = apcu_fetch ( $key );
        if (! $rs) {
            $rs = parent::find ( $parameters );
            apcu_store ( $key, $rs );
        }
        return $rs;
    }

    public static function findFirst($parameters = null) {
        $key = 'files_sizes_find_first.' . md5 ( json_encode ( $parameters ) );
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
    public function initialize()
    {
        parent::initialize();

    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'files_sizes';
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
            'key' => 'key',
            'max_width' => 'max_width',
            'max_height' => 'max_height',
            'crop' => 'crop',
            'attivo' => 'attivo'
        ];
    }

}
