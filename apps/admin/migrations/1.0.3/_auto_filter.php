<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class AutoFilterMigration_103
 */
class AutoFilterMigration_103 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('_auto_filter', [
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
                        'marca',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'id_post'
                        ]
                    ),
                    new Column(
                        'key_marca',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'marca'
                        ]
                    ),
                    new Column(
                        'modello',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_marca'
                        ]
                    ),
                    new Column(
                        'key_modello',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'modello'
                        ]
                    ),
                    new Column(
                        'alimentazione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_modello'
                        ]
                    ),
                    new Column(
                        'key_alimentazione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'alimentazione'
                        ]
                    ),
                    new Column(
                        'auto',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_alimentazione'
                        ]
                    ),
                    new Column(
                        'key_auto',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'auto'
                        ]
                    ),
                    new Column(
                        'anno_immatricolazione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_auto'
                        ]
                    ),
                    new Column(
                        'key_anno_immatricolazione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'anno_immatricolazione'
                        ]
                    ),
                    new Column(
                        'km',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_anno_immatricolazione'
                        ]
                    ),
                    new Column(
                        'key_km',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'km'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], 'UNIQUE'),
                    new Index('key_marca', ['key_marca'], null),
                    new Index('key_modello', ['key_modello'], null),
                    new Index('key_alimentazione', ['key_alimentazione'], null),
                    new Index('key_auto', ['key_auto'], null),
                    new Index('key_anno_immatricolazione', ['key_anno_immatricolazione'], null),
                    new Index('key_km', ['key_km'], null)
                ],
                'references' => [
                    new Reference(
                        '_auto_filter_post_fk',
                        [
                            'referencedTable' => '_auto',
                            'columns' => ['id_post'],
                            'referencedColumns' => ['id_post'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '442',
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
