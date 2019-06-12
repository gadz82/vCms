<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FiltriValoriMigration_109
 */
class FiltriValoriMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('filtri_valori', [
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
                        'id_filtro',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_filtro_valore_parent',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size'     => 11,
                            'after'    => 'id_filtro'
                        ]
                    ),
                    new Column(
                        'valore',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 255,
                            'after'   => 'id_filtro_valore_parent'
                        ]
                    ),
                    new Column(
                        'key',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 75,
                            'after'   => 'valore'
                        ]
                    ),
                    new Column(
                        'numeric_key',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 75,
                            'after' => 'key'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'numeric_key'
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
                            'type'    => Column::TYPE_INTEGER,
                            'default' => "1",
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('FK_filtri_valori_filtri_valori', ['id_filtro_valore_parent'], null),
                    new Index('FK_filtri_valori_filtri', ['id_filtro'], null),
                    new Index('key', ['key'], null),
                    new Index('numeric_key', ['numeric_key'], null),
                    new Index('valore', ['valore'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_filtri_valori_filtri',
                        [
                            'referencedTable'   => 'filtri',
                            'columns'           => ['id_filtro'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'FK_filtri_valori_filtri_valori',
                        [
                            'referencedTable'   => 'filtri_valori',
                            'columns'           => ['id_filtro_valore_parent'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '13',
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
