<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FormsMigration_109
 */
class FormsMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('forms', [
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
                        'id_tipologia_form',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_tipologia_stato',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 2,
                            'after'    => 'id_tipologia_form'
                        ]
                    ),
                    new Column(
                        'titolo',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 125,
                            'after'   => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'testo',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'titolo'
                        ]
                    ),
                    new Column(
                        'key',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 100,
                            'after'   => 'testo'
                        ]
                    ),
                    new Column(
                        'email_to',
                        [
                            'type'  => Column::TYPE_VARCHAR,
                            'size'  => 175,
                            'after' => 'key'
                        ]
                    ),
                    new Column(
                        'email_cc',
                        [
                            'type'  => Column::TYPE_TEXT,
                            'size'  => 1,
                            'after' => 'email_to'
                        ]
                    ),
                    new Column(
                        'email_bcc',
                        [
                            'type'  => Column::TYPE_TEXT,
                            'size'  => 1,
                            'after' => 'email_cc'
                        ]
                    ),
                    new Column(
                        'invio_utente',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'email_bcc'
                        ]
                    ),
                    new Column(
                        'submit_label',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size'    => 50,
                            'after'   => 'invio_utente'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'submit_label'
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
                        'id_utente',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 4,
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
                            'size'     => 4,
                            'after'    => 'id_utente'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_forms_tipologie_form', ['id_tipologia_form'], null),
                    new Index('fk_forms_tipologie_stato_form', ['id_tipologia_stato'], null),
                    new Index('key', ['key'], null),
                    new Index('fk_forms_utenti', ['id_utente'], null),
                    new Index('attivo', ['attivo'], null)
                ],
                'references' => [
                    new Reference(
                        'fk_forms_tipologie_form',
                        [
                            'referencedTable'   => 'tipologie_form',
                            'columns'           => ['id_tipologia_form'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_forms_tipologie_stato_form',
                        [
                            'referencedTable'   => 'tipologie_stato_form',
                            'columns'           => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_forms_utenti',
                        [
                            'referencedTable'   => 'utenti',
                            'columns'           => ['id_utente'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '2',
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
