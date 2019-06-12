<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItPaginaMigration_111
 */
class ItPaginaMigration_111 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('_it_pagina', [
                'columns' => [
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
                        'id_post',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
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
                            'after'    => 'id_post'
                        ]
                    ),
                    new Column(
                        'id_users_groups',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 75,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 150,
                            'after'   => 'id_users_groups'
                        ]
                    ),
                    new Column(
                        'slug',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 150,
                            'after'   => 'titolo'
                        ]
                    ),
                    new Column(
                        'excerpt',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 255,
                            'after'   => 'slug'
                        ]
                    ),
                    new Column(
                        'testo',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'excerpt'
                        ]
                    ),
                    new Column(
                        'data_inizio_pubblicazione',
                        [
                            'type'  => Column::TYPE_DATETIME,
                            'size'  => 1,
                            'after' => 'testo'
                        ]
                    ),
                    new Column(
                        'data_fine_pubblicazione',
                        [
                            'type'  => Column::TYPE_DATETIME,
                            'size'  => 1,
                            'after' => 'data_inizio_pubblicazione'
                        ]
                    ),
                    new Column(
                        'timestamp',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'data_fine_pubblicazione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'timestamp'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], 'UNIQUE'),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('slug', ['slug'], null),
                    new Index('attivo', ['attivo'], null),
                    new Index('titolo', ['titolo'], 'FULLTEXT'),
                    new Index('excerpt', ['excerpt'], 'FULLTEXT')
                ],
                'options' => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '4',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'latin1_swedish_ci'
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
