<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class RuoliMenuMigration_106
 */
class RuoliMenuMigration_106 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('ruoli_menu', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 4,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_ruolo',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 3,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'livello',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "0",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_ruolo'
                        ]
                    ),
                    new Column(
                        'risorsa',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'livello'
                        ]
                    ),
                    new Column(
                        'azione',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'risorsa'
                        ]
                    ),
                    new Column(
                        'descrizione',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'azione'
                        ]
                    ),
                    new Column(
                        'class',
                        [
                            'type'    => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'descrizione'
                        ]
                    ),
                    new Column(
                        'header',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'class'
                        ]
                    ),
                    new Column(
                        'visible',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'header'
                        ]
                    ),
                    new Column(
                        'id_padre',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 4,
                            'after'    => 'visible'
                        ]
                    ),
                    new Column(
                        'ordine',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_padre'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'ordine'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'data_creazione'
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
                            'after'    => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_ruoli_menu_ruoli', ['id_ruolo'], null),
                    new Index('attivo', ['attivo'], null),
                    new Index('id_padre', ['id_padre'], null),
                    new Index('risorsa', ['risorsa'], null),
                    new Index('azione', ['azione'], null),
                    new Index('livello', ['livello'], null)
                ],
                'references' => [
                    new Reference(
                        'fk_ruoli_menu_ruoli',
                        [
                            'referencedTable'   => 'ruoli',
                            'columns'           => ['id_ruolo'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '117',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_swedish_ci'
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
