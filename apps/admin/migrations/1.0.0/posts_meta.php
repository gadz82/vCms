<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PostsMetaMigration_100
 */
class PostsMetaMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('posts_meta', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_BIGINTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 20,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'post_id',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_tipologia_post_meta',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'post_id'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_tipologia_post_meta'
                        ]
                    ),
                    new Column(
                        'id_meta',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'meta_key',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size'    => 175,
                            'after'   => 'id_meta'
                        ]
                    ),
                    new Column(
                        'meta_value_int',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size'     => 11,
                            'after'    => 'meta_key'
                        ]
                    ),
                    new Column(
                        'meta_value_decimal',
                        [
                            'type'     => Column::TYPE_DECIMAL,
                            'unsigned' => true,
                            'size'     => 14,
                            'scale'    => 6,
                            'after'    => 'meta_value_int'
                        ]
                    ),
                    new Column(
                        'meta_value_varchar',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 255,
                            'after' => 'meta_value_decimal'
                        ]
                    ),
                    new Column(
                        'meta_value_text',
                        [
                            'type'  => Column::TYPE_TEXT,
                            'size'  => 1,
                            'after' => 'meta_value_varchar'
                        ]
                    ),
                    new Column(
                        'meta_value_datetime',
                        [
                            'type'  => Column::TYPE_DATETIME,
                            'size'  => 1,
                            'after' => 'meta_value_text'
                        ]
                    ),
                    new Column(
                        'meta_value_files',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size'     => 11,
                            'after'    => 'meta_value_datetime'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'meta_value_files'
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
                        'autoload',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "0",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'data_aggiornamento'
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
                            'after'    => 'autoload'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('post_id_unique', ['post_id', 'id_meta', 'attivo', 'id_tipologia_stato'], 'UNIQUE'),
                    new Index('post_id', ['post_id'], null),
                    new Index('id_tipologia_post_meta', ['id_tipologia_post_meta'], null),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('id_meta', ['id_meta'], null),
                    new Index('meta_key', ['meta_key'], null),
                    new Index('meta_value_files', ['meta_value_files'], null)
                ],
                'references' => [
                    new Reference(
                        'posts_meta_ibfk_1',
                        [
                            'referencedTable'   => 'posts',
                            'columns'           => ['post_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_meta_ibfk_2',
                        [
                            'referencedTable'   => 'tipologie_meta',
                            'columns'           => ['id_tipologia_post_meta'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_meta_ibfk_3',
                        [
                            'referencedTable'   => 'tipologie_stato_post_meta',
                            'columns'           => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_meta_ibfk_4',
                        [
                            'referencedTable'   => 'meta',
                            'columns'           => ['id_meta'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '62',
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
