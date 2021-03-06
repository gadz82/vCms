<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class EventiMetaMigration_103
 */
class EventiMetaMigration_103 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('_eventi_meta', [
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
                        'id_post',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'meta_title',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'id_post'
                        ]
                    ),
                    new Column(
                        'meta_description',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'meta_title'
                        ]
                    ),
                    new Column(
                        'og_title',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'meta_description'
                        ]
                    ),
                    new Column(
                        'og_description',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'og_title'
                        ]
                    ),
                    new Column(
                        'og_image',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'og_description'
                        ]
                    ),
                    new Column(
                        'robots',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'og_image'
                        ]
                    ),
                    new Column(
                        'video_url',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'robots'
                        ]
                    ),
                    new Column(
                        'immagine',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'video_url'
                        ]
                    ),
                    new Column(
                        'immagini_gallery',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'immagine'
                        ]
                    ),
                    new Column(
                        'data_inizio_evento',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'immagini_gallery'
                        ]
                    ),
                    new Column(
                        'data_fine_evento',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'data_inizio_evento'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], 'UNIQUE')
                ],
                'references' => [
                    new Reference(
                        '_eventi_meta_post_fk',
                        [
                            'referencedTable' => '_eventi',
                            'columns' => ['id_post'],
                            'referencedColumns' => ['id_post'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '2',
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
