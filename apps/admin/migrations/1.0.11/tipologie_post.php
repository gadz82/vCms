<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class TipologiePostMigration_111
 */
class TipologiePostMigration_111 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('tipologie_post', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 2,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'descrizione',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 175,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'slug',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 75,
                            'after' => 'descrizione'
                        ]
                    ),
                    new Column(
                        'admin_menu',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'slug'
                        ]
                    ),
                    new Column(
                        'admin_icon',
                        [
                            'type' => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size' => 50,
                            'after' => 'admin_menu'
                        ]
                    ),
                    new Column(
                        'ordine',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'admin_icon'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'size' => 1,
                            'after' => 'ordine'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('slug_tp', ['slug'], 'UNIQUE')
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '10',
                    'ENGINE' => 'InnoDB',
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
