<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class TipologiePuntoVenditaMigration_108
 */
class TipologiePuntoVenditaMigration_108 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('tipologie_punto_vendita', [
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
                        'codice',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 50,
                            'after' => 'descrizione'
                        ]
                    ),
                    new Column(
                        'ordine',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'codice'
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
                    new Index('attivo', ['attivo'], null)
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '5',
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
