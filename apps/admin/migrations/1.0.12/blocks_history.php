<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class BlocksHistoryMigration_112
 */
class BlocksHistoryMigration_112 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('blocks_history', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'id_block',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'id_applicazione',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_block'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_applicazione'
                        ]
                    ),
                    new Column(
                        'id_tipologia_block',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'id_block_tag',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_tipologia_block'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 150,
                            'after' => 'id_block_tag'
                        ]
                    ),
                    new Column(
                        'key',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 75,
                            'after' => 'titolo'
                        ]
                    ),
                    new Column(
                        'content',
                        [
                            'type' => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'key'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'content'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'size' => 1,
                            'after' => 'data_creazione'
                        ]
                    ),
                    new Column(
                        'data_inizio_pubblicazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'data_aggiornamento'
                        ]
                    ),
                    new Column(
                        'data_fine_pubblicazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'data_inizio_pubblicazione'
                        ]
                    ),
                    new Column(
                        'id_utente',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 4,
                            'after' => 'data_fine_pubblicazione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'id_utente'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('attivo', ['attivo'], null),
                    new Index('key', ['key'], null),
                    new Index('fk_blocks_applicazioni', ['id_applicazione'], null),
                    new Index('fk_blocks_utenti', ['id_utente'], null),
                    new Index('fk_blocks_tipologie_stato_block', ['id_tipologia_stato'], null),
                    new Index('fk_blocks_tipologie_block', ['id_tipologia_block'], null),
                    new Index('id_block_tag', ['id_block_tag'], null),
                    new Index('id_block', ['id_block'], null)
                ],
                'references' => [
                    new Reference(
                        'blocks_history_ibfk_1',
                        [
                            'referencedTable' => 'blocks',
                            'columns' => ['id_block'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '',
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
