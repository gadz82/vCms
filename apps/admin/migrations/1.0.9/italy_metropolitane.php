<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyMetropolitaneMigration_109
 */
class ItalyMetropolitaneMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_metropolitane', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 11,
                            'first'   => true
                        ]
                    ),
                    new Column(
                        'denominazione',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'capoluogo',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'denominazione'
                        ]
                    ),
                    new Column(
                        'popolazione',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 11,
                            'after' => 'capoluogo'
                        ]
                    ),
                    new Column(
                        'superficie',
                        [
                            'type'  => Column::TYPE_DOUBLE,
                            'size'  => 1,
                            'after' => 'popolazione'
                        ]
                    ),
                    new Column(
                        'densita',
                        [
                            'type'  => Column::TYPE_DOUBLE,
                            'size'  => 1,
                            'after' => 'superficie'
                        ]
                    ),
                    new Column(
                        'numero_comuni',
                        [
                            'type'  => Column::TYPE_INTEGER,
                            'size'  => 11,
                            'after' => 'densita'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY')
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
