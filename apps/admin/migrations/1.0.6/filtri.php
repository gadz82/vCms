<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FiltriMigration_106
 */
class FiltriMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('filtri', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 11,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_applicazione',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_filtri_group',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_applicazione'
                        ]
                    ),
                    new Column(
                        'id_tipologia_filtro',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_filtri_group'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_tipologia_filtro'
                        ]
                    ),
                    new Column(
                        'id_filtro_parent',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size'     => 11,
                            'after'    => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'key',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 175,
                            'after'   => 'id_filtro_parent'
                        ]
                    ),
                    new Column(
                        'one_to_one',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'key'
                        ]
                    ),
                    new Column(
                        'required',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'one_to_one'
                        ]
                    ),
                    new Column(
                        'frontend_filter',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'required'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 100,
                            'after'   => 'frontend_filter'
                        ]
                    ),
                    new Column(
                        'descrizione',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'titolo'
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
                        'id_utente',
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
                            'after'   => 'id_utente'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_filtri_filtri_group', ['id_filtri_group'], null),
                    new Index('fk_filtri_tipologie_filtro', ['id_tipologia_filtro'], null),
                    new Index('fk_filtri_tipologie_stato_filtro', ['id_tipologia_stato'], null),
                    new Index('fk_filtri_utenti', ['id_utente'], null),
                    new Index('fk_filtri_filtri', ['id_filtro_parent'], null),
                    new Index('key', ['key'], null),
                    new Index('fk_filtri_applicazioni', ['id_applicazione'], null)
                ],
                'references' => [
                    new Reference(
                        'fk_filtri_applicazioni',
                        [
                            'referencedTable'   => 'applicazioni',
                            'columns'           => ['id_applicazione'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_filtri',
                        [
                            'referencedTable'   => 'filtri',
                            'columns'           => ['id_filtro_parent'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_filtri_group',
                        [
                            'referencedTable'   => 'filtri_group',
                            'columns'           => ['id_filtri_group'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_tipologie_filtro',
                        [
                            'referencedTable'   => 'tipologie_filtro',
                            'columns'           => ['id_tipologia_filtro'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_tipologie_stato_filtro',
                        [
                            'referencedTable'   => 'tipologie_stato_filtro',
                            'columns'           => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_utenti',
                        [
                            'referencedTable'   => 'utenti',
                            'columns'           => ['id_utente'],
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
