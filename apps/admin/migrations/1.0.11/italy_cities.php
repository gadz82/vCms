<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyCitiesMigration_111
 */
class ItalyCitiesMigration_111 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_cities', [
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
                        'regione',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 50,
                            'after' => 'comune'
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
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['istat'], 'PRIMARY')
                ],
                'options' => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '',
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
