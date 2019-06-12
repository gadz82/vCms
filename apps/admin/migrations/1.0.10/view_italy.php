<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ViewItalyMigration_110
 */
class ViewItalyMigration_110 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('view_italy', [
                'columns' => [
                    new Column(
                        'istat',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 11,
                            'first'   => true
                        ]
                    ),
                    new Column(
                        'comune',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'istat'
                        ]
                    ),
                    new Column(
                        'cap',
                        [
                            'type'  => Column::TYPE_CHAR,
                            'size'  => 11,
                            'after' => 'comune'
                        ]
                    ),
                    new Column(
                        'regione',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 50,
                            'after' => 'cap'
                        ]
                    ),
                    new Column(
                        'provincia',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 2,
                            'after' => 'regione'
                        ]
                    ),
                    new Column(
                        'prefisso',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 7,
                            'after' => 'provincia'
                        ]
                    ),
                    new Column(
                        'cod_fisco',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 10,
                            'after' => 'prefisso'
                        ]
                    ),
                    new Column(
                        'superficie',
                        [
                            'type'  => Column::TYPE_DOUBLE,
                            'size'  => 1,
                            'after' => 'cod_fisco'
                        ]
                    ),
                    new Column(
                        'num_residenti',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 11,
                            'after' => 'superficie'
                        ]
                    ),
                    new Column(
                        'lng',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'num_residenti'
                        ]
                    ),
                    new Column(
                        'lat',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'lng'
                        ]
                    ),
                    new Column(
                        'abitanti',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'lat'
                        ]
                    ),
                    new Column(
                        'patrono_nome',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'abitanti'
                        ]
                    ),
                    new Column(
                        'patrono_data',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'patrono_nome'
                        ]
                    ),
                    new Column(
                        'indirizzo',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'patrono_data'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE'      => 'VIEW',
                    'AUTO_INCREMENT'  => '',
                    'ENGINE'          => '',
                    'TABLE_COLLATION' => ''
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
