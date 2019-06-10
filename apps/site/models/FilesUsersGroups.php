<?php

class FilesUsersGroups extends BaseModel
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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id_file;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id_user_group;

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
     * @return FilesUsersGroups[]|FilesUsersGroups
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FilesUsersGroups
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
        $this->belongsTo('id_user_group', '\UsersGroups', 'id', ['alias' => 'UsersGroups', 'reusable' => true]);
        $this->belongsTo('id', '\Files', 'id', ['alias' => 'Files', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'files_users_groups';
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'id_file' => 'id_file',
            'id_user_group' => 'id_user_group',
            'attivo' => 'attivo'
        ];
    }

}
