<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PostsMigration_109
 */
class PostsMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('posts', [
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
                        'id_applicazione',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id'
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
                        'id_tipologia_post',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 150,
                            'after' => 'id_tipologia_post'
                        ]
                    ),
                    new Column(
                        'slug',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 75,
                            'after' => 'titolo'
                        ]
                    ),
                    new Column(
                        'excerpt',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'size' => 275,
                            'after' => 'slug'
                        ]
                    ),
                    new Column(
                        'testo',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'excerpt'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'testo'
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
                    new Index('slug_unique', ['slug', 'attivo', 'id_applicazione'], 'UNIQUE'),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('id_tipologia_post', ['id_tipologia_post'], null),
                    new Index('id_utente', ['id_utente'], null),
                    new Index('id_applicazione', ['id_applicazione'], null),
                    new Index('slug', ['slug'], null),
                    new Index('attivo', ['attivo'], null)
                ],
                'references' => [
                    new Reference(
                        'posts_ibfk_1',
                        [
                            'referencedTable' => 'tipologie_stato_post',
                            'columns' => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_ibfk_2',
                        [
                            'referencedTable' => 'tipologie_post',
                            'columns' => ['id_tipologia_post'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_ibfk_3',
                        [
                            'referencedTable' => 'utenti',
                            'columns' => ['id_utente'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'posts_ibfk_4',
                        [
                            'referencedTable' => 'applicazioni',
                            'columns' => ['id_applicazione'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '13',
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
