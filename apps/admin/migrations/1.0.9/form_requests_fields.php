<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class FormRequestsFieldsMigration_109
 */
class FormRequestsFieldsMigration_109 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('form_requests_fields', [
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
                        'id_form_request',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "0",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id'
                        ]
                    ),
                    new Column(
                        'id_form',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id_form_request'
                        ]
                    ),
                    new Column(
                        'id_form_field',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 11,
                            'after'    => 'id_form'
                        ]
                    ),
                    new Column(
                        'input_value',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'id_form_field'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size'    => 1,
                            'after'   => 'input_value'
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
                        'attivo',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'default'  => "1",
                            'unsigned' => true,
                            'notNull'  => true,
                            'size'     => 1,
                            'after'    => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes'    => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('fk_form_requests_fields_form_requests', ['id_form_request'], null),
                    new Index('fk_form_requests_fields_forms', ['id_form'], null),
                    new Index('fk_form_requests_fields_form_fields', ['id_form_field'], null),
                    new Index('attivo', ['attivo'], null)
                ],
                'references' => [
                    new Reference(
                        'fk_form_requests_fields_form_fields',
                        [
                            'referencedTable'   => 'form_fields',
                            'columns'           => ['id_form_field'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_form_requests_fields_form_requests',
                        [
                            'referencedTable'   => 'form_requests',
                            'columns'           => ['id_form_request'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'fk_form_requests_fields_forms',
                        [
                            'referencedTable'   => 'forms',
                            'columns'           => ['id_form'],
                            'referencedColumns' => ['id'],
                            'onUpdate'          => 'CASCADE',
                            'onDelete'          => 'NO ACTION'
                        ]
                    )
                ],
                'options'    => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'AUTO_INCREMENT'  => '1',
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
