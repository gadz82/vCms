<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PostsUsersGroupsMigration_110
 */
class PostsUsersGroupsMigration_110 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('posts_users_groups', [
                'columns'    => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'size'          => 11,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'id_post',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_user_group',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 4,
                            'after'    => 'id_post'
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
                            'after'    => 'id_user_group'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_post', ['id_post'], null),
                    new Index('id_user_group', ['id_user_group'], null),
                    new Index('attivo', ['attivo'], null)
                ],
                'references' => [
                    new Reference(
                        'posts_users_groups_ibfk_1',
                        [
                            'referencedTable'   => 'posts',
                            'columns'           => ['id_post'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'posts_users_groups_ibfk_2',
                        [
                            'referencedTable'   => 'users_groups',
                            'columns'           => ['id_user_group'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '6',
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
