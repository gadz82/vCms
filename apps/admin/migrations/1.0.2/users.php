<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class UsersMigration_102
 */
class UsersMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('users', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'id_users_groups',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 4,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_users_groups'
                        ]
                    ),
                    new Column(
                        'username',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 125,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'email',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 125,
                            'after' => 'username'
                        ]
                    ),
                    new Column(
                        'nome',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 250,
                            'after' => 'email'
                        ]
                    ),
                    new Column(
                        'cognome',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 250,
                            'after' => 'nome'
                        ]
                    ),
                    new Column(
                        'telefono',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'cognome'
                        ]
                    ),
                    new Column(
                        'indirizzo',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 250,
                            'after' => 'telefono'
                        ]
                    ),
                    new Column(
                        'localita',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 125,
                            'after' => 'indirizzo'
                        ]
                    ),
                    new Column(
                        'cap',
                        [
                            'type' => Column::TYPE_CHAR,
                            'size' => 5,
                            'after' => 'localita'
                        ]
                    ),
                    new Column(
                        'data_di_nascita',
                        [
                            'type' => Column::TYPE_DATE,
                            'size' => 1,
                            'after' => 'cap'
                        ]
                    ),
                    new Column(
                        'validation_token',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'data_di_nascita'
                        ]
                    ),
                    new Column(
                        'token_validated',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'validation_token'
                        ]
                    ),
                    new Column(
                        'password',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 32,
                            'after' => 'token_validated'
                        ]
                    ),
                    new Column(
                        'password_reset_token',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'password'
                        ]
                    ),
                    new Column(
                        'validation_expiration_date',
                        [
                            'type' => Column::TYPE_DATE,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'password_reset_token'
                        ]
                    ),
                    new Column(
                        'reset_password_expiration_date',
                        [
                            'type' => Column::TYPE_DATE,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'validation_expiration_date'
                        ]
                    ),
                    new Column(
                        'user_registration_date',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'reset_password_expiration_date'
                        ]
                    ),
                    new Column(
                        'user_last_login',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'user_registration_date'
                        ]
                    ),
                    new Column(
                        'ip_address',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'size' => 25,
                            'after' => 'user_last_login'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'ip_address'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'size' => 1,
                            'after' => 'data_creazione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('email', ['email', 'attivo'], 'UNIQUE'),
                    new Index('username', ['username', 'attivo'], 'UNIQUE'),
                    new Index('id_users_groups', ['id_users_groups'], null),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('attivo', ['attivo'], null)
                ],
                'references' => [
                    new Reference(
                        'users_ibfk_1',
                        [
                            'referencedTable' => 'users_groups',
                            'columns' => ['id_users_groups'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'users_ibfk_2',
                        [
                            'referencedTable' => 'tipologie_stato_user',
                            'columns' => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
