<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FilesMigration_104
 */
class FilesMigration_104 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('files', [
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
                        'id_tipologia_stato',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'original_filename',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 100,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'filename',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 100,
                            'after' => 'original_filename'
                        ]
                    ),
                    new Column(
                        'filetype',
                        [
                            'type' => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size' => 20,
                            'after' => 'filename'
                        ]
                    ),
                    new Column(
                        'filesize',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 10,
                            'after' => 'filetype'
                        ]
                    ),
                    new Column(
                        'filepath',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'filesize'
                        ]
                    ),
                    new Column(
                        'fileurl',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'filepath'
                        ]
                    ),
                    new Column(
                        'priorita',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'fileurl'
                        ]
                    ),
                    new Column(
                        'alt',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'priorita'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'alt'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'data_creazione'
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
                            'after' => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('filename', ['filename'], null)
                ],
                'references' => [
                    new Reference(
                        'files_ibfk_1',
                        [
                            'referencedTable' => 'tipologie_stato_file',
                            'columns' => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '19',
                    'ENGINE' => 'InnoDB',
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
