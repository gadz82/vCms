<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class UtentiMigration_103
 */
class UtentiMigration_103 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('utenti', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 4,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_tipologia_utente',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'id_tipologia_utente'
                        ]
                    ),
                    new Column(
                        'id_ruolo',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 3,
                            'after'    => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'livello',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "0",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_ruolo'
                        ]
                    ),
                    new Column(
                        'nome_utente',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'livello'
                        ]
                    ),
                    new Column(
                        'password',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 60,
                            'after'   => 'nome_utente'
                        ]
                    ),
                    new Column(
                        'nome',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'password'
                        ]
                    ),
                    new Column(
                        'cognome',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'nome'
                        ]
                    ),
                    new Column(
                        'email',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 100,
                            'after'   => 'cognome'
                        ]
                    ),
                    new Column(
                        'avatar',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'email'
                        ]
                    ),
                    new Column(
                        'token',
                        [
                            'type'  => Column::TYPE_CHAR,
                            'size'  => 50,
                            'after' => 'avatar'
                        ]
                    ),
                    new Column(
                        'api_level',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size'    => 2,
                            'after'   => 'token'
                        ]
                    ),
                    new Column(
                        'public_key',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 75,
                            'after' => 'api_level'
                        ]
                    ),
                    new Column(
                        'private_key',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 75,
                            'after' => 'public_key'
                        ]
                    ),
                    new Column(
                        'data_creazione_token',
                        [
                            'type'  => Column::TYPE_DATETIME,
                            'size'  => 1,
                            'after' => 'private_key'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'data_creazione_token'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'data_creazione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "1",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_utenti_tipologie_utente', ['id_tipologia_utente'], null),
                    new Index('fk_utenti_tipologie_stato_utente', ['id_tipologia_stato'], null),
                    new Index('fk_utenti_ruoli', ['id_ruolo'], null),
                    new Index('attivo', ['attivo'], null),
                    new Index('public_key', ['public_key'], null)
                ],
                'references' => [
                    new Reference(
                        'utenti_ibfk_1',
                        [
                            'referencedTable'   => 'tipologie_utente',
                            'columns'           => ['id_tipologia_utente'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'utenti_ibfk_2',
                        [
                            'referencedTable'   => 'tipologie_stato_utente',
                            'columns'           => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'utenti_ibfk_3',
                        [
                            'referencedTable'   => 'ruoli',
                            'columns'           => ['id_ruolo'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '9',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_swedish_ci'
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
