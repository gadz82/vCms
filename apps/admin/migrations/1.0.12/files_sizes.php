<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FilesSizesMigration_112
 */
class FilesSizesMigration_112 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('files_sizes', [
                'columns' => [
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
                        'key',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'id'
                        ]
                    ),
                    new Column(
                        'max_width',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'key'
                        ]
                    ),
                    new Column(
                        'max_height',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'max_width'
                        ]
                    ),
                    new Column(
                        'crop',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'max_height'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "1",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'crop'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('key', ['key'], null)
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
