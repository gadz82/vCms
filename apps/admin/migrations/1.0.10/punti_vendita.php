<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PuntiVenditaMigration_110
 */
class PuntiVenditaMigration_110 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('punti_vendita', [
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
                        'id_tipologia_punto_vendita',
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
                            'after' => 'id_tipologia_punto_vendita'
                        ]
                    ),
                    new Column(
                        'id_regione',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 2,
                            'after' => 'id_tipologia_stato'
                        ]
                    ),
                    new Column(
                        'comune',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'id_regione'
                        ]
                    ),
                    new Column(
                        'nome',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'default' => "",
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'comune'
                        ]
                    ),
                    new Column(
                        'data',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'nome'
                        ]
                    ),
                    new Column(
                        'lat',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'notNull' => true,
                            'size' => 10,
                            'scale' => 8,
                            'after' => 'data'
                        ]
                    ),
                    new Column(
                        'lng',
                        [
                            'type' => Column::TYPE_DECIMAL,
                            'notNull' => true,
                            'size' => 11,
                            'scale' => 8,
                            'after' => 'lat'
                        ]
                    ),
                    new Column(
                        'address',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 250,
                            'after' => 'lng'
                        ]
                    ),
                    new Column(
                        'id_pdv',
                        [
                            'type' => Column::TYPE_CHAR,
                            'size' => 6,
                            'after' => 'address'
                        ]
                    ),
                    new Column(
                        'coop',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 25,
                            'after' => 'id_pdv'
                        ]
                    ),
                    new Column(
                        'data_creazione',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'coop'
                        ]
                    ),
                    new Column(
                        'data_aggiornamento',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'size' => 1,
                            'after' => 'data_creazione'
                        ]
                    ),
                    new Column(
                        'attivo',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'data_aggiornamento'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('id_tipologia_punto_vendita', ['id_tipologia_punto_vendita'], null),
                    new Index('id_tipologia_stato', ['id_tipologia_stato'], null),
                    new Index('id_regione', ['id_regione'], null),
                    new Index('attivo', ['attivo'], null),
                    new Index('id_pdv', ['id_pdv'], null),
                    new Index('coop', ['coop'], null),
                    new Index('comune', ['comune'], null)
                ],
                'references' => [
                    new Reference(
                        'punti_vendita_ibfk_1',
                        [
                            'referencedTable' => 'tipologie_punto_vendita',
                            'columns' => ['id_tipologia_punto_vendita'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'punti_vendita_ibfk_3',
                        [
                            'referencedTable' => 'regioni_pac',
                            'columns' => ['id_regione'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'punti_vendita_ibfk_4',
                        [
                            'referencedTable' => 'tipologie_stato_punto_vendita',
                            'columns' => ['id_tipologia_stato'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1039',
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
