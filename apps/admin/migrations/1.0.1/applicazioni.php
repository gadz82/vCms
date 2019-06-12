<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ApplicazioniMigration_101
 */
class ApplicazioniMigration_101 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('applicazioni', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 2,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_tipologia_applicazione',
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
                            'size'     => 2,
                            'after'    => 'id_tipologia_applicazione'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size'    => 75,
                            'after'   => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'codice',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'default' => "",
                            'notNull' => true,
                            'size'    => 5,
                            'after'   => 'titolo'
                        ]
                    ),
                    new Column(
                        'href_lang',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size'    => 6,
                            'after'   => 'codice'
                        ]
                    ),
                    new Column(
                        'descrizione',
                        [
                            'type'  => Column::TYPE_TEXT,
                            'size'  => 1,
                            'after' => 'href_lang'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'descrizione'
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
                        'id_utente_admin',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 4,
                            'after'    => 'data_aggiornamento'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'default' => "1",
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'id_utente_admin'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_tipologia_applicazione', ['id_tipologia_applicazione'], null),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('id_utente_admin', ['id_utente_admin'], null),
                    new Index('codice', ['codice'], null)
                ],
                'references' => [
                    new Reference(
                        'applicazioni_ibfk_1',
                        [
                            'referencedTable'   => 'tipologie_applicazione',
                            'columns'           => ['id_tipologia_applicazione'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'applicazioni_ibfk_2',
                        [
                            'referencedTable'   => 'tipologie_stato_applicazione',
                            'columns'           => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'applicazioni_ibfk_3',
                        [
                            'referencedTable'   => 'utenti',
                            'columns'           => ['id_utente_admin'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '3',
                    'ENGINE'          => 'InnoDB',
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
