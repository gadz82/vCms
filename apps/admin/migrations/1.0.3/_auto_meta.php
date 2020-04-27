<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class AutoMetaMigration_103
 */
class AutoMetaMigration_103 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('_auto_meta', [
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
                        'model',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'immagini_gallery'
                        ]
                    ),
                    new Column(
                        'short_model',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'model'
                        ]
                    ),
                    new Column(
                        'version',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'short_model'
                        ]
                    ),
                    new Column(
                        'customers_price',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'size' => 12,
                            'scale' => 6,
                            'after' => 'version'
                        ]
                    ),
                    new Column(
                        'dealers_price',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'size' => 12,
                            'scale' => 6,
                            'after' => 'customers_price'
                        ]
                    ),
                    new Column(
                        'new_price',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'size' => 12,
                            'scale' => 6,
                            'after' => 'dealers_price'
                        ]
                    ),
                    new Column(
                        'warranty',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'new_price'
                        ]
                    ),
                    new Column(
                        'warranty_months',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'warranty'
                        ]
                    ),
                    new Column(
                        'optionals',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'warranty_months'
                        ]
                    ),
                    new Column(
                        'additional_informations',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'optionals'
                        ]
                    ),
                    new Column(
                        'seats',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'additional_informations'
                        ]
                    ),
                    new Column(
                        'cc',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'seats'
                        ]
                    ),
                    new Column(
                        'kwatt',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'cc'
                        ]
                    ),
                    new Column(
                        'cvfiscali',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'kwatt'
                        ]
                    ),
                    new Column(
                        'cylinders',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'cvfiscali'
                        ]
                    ),
                    new Column(
                        'km',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'cylinders'
                        ]
                    ),
                    new Column(
                        'gearbox',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'km'
                        ]
                    ),
                    new Column(
                        'gears_number',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'gearbox'
                        ]
                    ),
                    new Column(
                        'urban',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'gears_number'
                        ]
                    ),
                    new Column(
                        'outer',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'urban'
                        ]
                    ),
                    new Column(
                        'combined',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'outer'
                        ]
                    ),
                    new Column(
                        'doors',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'combined'
                        ]
                    ),
                    new Column(
                        'color',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'doors'
                        ]
                    ),
                    new Column(
                        'paint',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'color'
                        ]
                    ),
                    new Column(
                        'id_gestionale_auto',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'paint'
                        ]
                    ),
                    new Column(
                        'referente',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'id_gestionale_auto'
                        ]
                    ),
                    new Column(
                        'telefono_referente',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'referente'
                        ]
                    ),
                    new Column(
                        'email_referente',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'telefono_referente'
                        ]
                    ),
                    new Column(
                        'indirizzo_sede',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'email_referente'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], 'UNIQUE')
                ],
                'references' => [
                    new Reference(
                        '_auto_meta_post_fk',
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
