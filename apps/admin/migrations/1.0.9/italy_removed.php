<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyRemovedMigration_109
 */
class ItalyRemovedMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_removed', [
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
                        'provincia',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 10,
                            'after' => 'comune'
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
