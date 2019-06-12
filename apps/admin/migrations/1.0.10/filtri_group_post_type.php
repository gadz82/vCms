<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FiltriGroupPostTypeMigration_110
 */
class FiltriGroupPostTypeMigration_110 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('filtri_group_post_type', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 6,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_tipologia_post',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_filtri_group',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_tipologia_post'
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
                            'after'    => 'id_filtri_group'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_meta_group_post_type_tipologie_post', ['id_tipologia_post'], null),
                    new Index('fk_meta_group_post_type_meta_group', ['id_filtri_group'], null)
                ],
                'references' => [
                    new Reference(
                        'fk_filtri_group_post_type_filtri_group',
                        [
                            'referencedTable'   => 'filtri_group',
                            'columns'           => ['id_filtri_group'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'fk_filtri_group_post_type_tipologie_post',
                        [
                            'referencedTable'   => 'tipologie_post',
                            'columns'           => ['id_tipologia_post'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'CASCADE'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '15',
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
