<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItProdottiFilterMigration_110
 */
class ItProdottiFilterMigration_110 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('_it_prodotti_filter', [
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
                        'regione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'id_post'
                        ]
                    ),
                    new Column(
                        'key_regione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'regione'
                        ]
                    ),
                    new Column(
                        'tipologia',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'key_regione'
                        ]
                    ),
                    new Column(
                        'key_tipologia',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'tipologia'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], 'UNIQUE'),
                    new Index('key_regione', ['key_regione'], null),
                    new Index('key_tipologia', ['key_tipologia'], null)
                ],
                'references' => [
                    new Reference(
                        '_it_prodotti_filter_post_fk',
                        [
                            'referencedTable' => '_it_prodotti',
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
                    'TABLE_COLLATION' => 'latin1_swedish_ci'
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