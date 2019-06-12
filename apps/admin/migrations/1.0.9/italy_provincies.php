<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyProvinciesMigration_109
 */
class ItalyProvinciesMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_provincies', [
                'columns' => [
                    new Column(
                        'sigla',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 2,
                            'first'   => true
                        ]
                    ),
                    new Column(
                        'provincia',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'sigla'
                        ]
                    ),
                    new Column(
                        'superficie',
                        [
                            'type'  => Column::TYPE_DOUBLE,
                            'size'  => 1,
                            'after' => 'provincia'
                        ]
                    ),
                    new Column(
                        'residenti',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 11,
                            'after' => 'superficie'
                        ]
                    ),
                    new Column(
                        'num_comuni',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 11,
                            'after' => 'residenti'
                        ]
                    ),
                    new Column(
                        'id_regione',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 6,
                            'after' => 'num_comuni'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['sigla'], 'PRIMARY')
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
