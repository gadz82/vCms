<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class TipologieVolantinoMigration_112
 */
class TipologieVolantinoMigration_112 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('tipologie_volantino', [
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
                        'descrizione',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'default' => "",
                            'notNull' => true,
                            'size'    => 175,
                            'after'   => 'id'
                        ]
                    ),
                    new Column(
                        'ordine',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size'    => 2,
                            'after'   => 'descrizione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'default' => "1",
                            'size'    => 1,
                            'after'   => 'ordine'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('attivo', ['attivo'], null)
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
