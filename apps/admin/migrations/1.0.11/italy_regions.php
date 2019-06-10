<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItalyRegionsMigration_111
 */
class ItalyRegionsMigration_111 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('italy_regions', [
                'columns' => [
                    new Column(
                        'id_regione',
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
                        'regione',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 50,
                            'after' => 'id_regione'
                        ]
                    ),
                    new Column(
                        'superficie',
                        [
                            'type' => Column::TYPE_DOUBLE,
                            'size' => 1,
                            'after' => 'regione'
                        ]
                    ),
                    new Column(
                        'num_residenti',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'superficie'
                        ]
                    ),
                    new Column(
                        'num_comuni',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'num_residenti'
                        ]
                    ),
                    new Column(
                        'num_provincie',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 11,
                            'after' => 'num_comuni'
                        ]
                    ),
                    new Column(
                        'presidente',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 45,
                            'after' => 'num_provincie'
                        ]
                    ),
                    new Column(
                        'cod_istat',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 2,
                            'after' => 'presidente'
                        ]
                    ),
                    new Column(
                        'cod_fiscale',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 11,
                            'after' => 'cod_istat'
                        ]
                    ),
                    new Column(
                        'piva',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 11,
                            'after' => 'cod_fiscale'
                        ]
                    ),
                    new Column(
                        'pec',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 100,
                            'after' => 'piva'
                        ]
                    ),
                    new Column(
                        'sito',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 100,
                            'after' => 'pec'
                        ]
                    ),
                    new Column(
                        'sede',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'sito'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id_regione'], 'PRIMARY')
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '21',
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
