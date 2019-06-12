<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyCapMigration_111
 */
class ItalyCapMigration_111 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_cap', [
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
                        'cap',
                        [
                            'type'  => Column::TYPE_CHAR,
                            'size'  => 11,
                            'after' => 'istat'
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
