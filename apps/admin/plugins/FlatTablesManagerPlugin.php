<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 13/04/17
 * Time: 11:36
 */

namespace apps\admin\plugins;

use apps\admin\library\ShortcodeManager;
use Phalcon\Annotations\Exception;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;


/**
 * Class FlatTablesManagerPlugin
 * Gestione Tabelle flata in base alle operations effettuate sui Post Types
 * @package apps\admin\plugins
 */
class FlatTablesManagerPlugin extends Plugin
{
    const meta_suffix = '_meta';
    const filtri_suffix = '_filter';
    /**
     * @var \apps\admin\library\ShortcodeManager
     */
    protected $shortcodeManager;
    /**
     * @var Mysql
     */
    private $connection;
    private $mySqlVersion;
    private $applications;

    /**
     * FlatTablesManagerPlugin constructor.
     */
    public function __construct()
    {
        $this->connection = $this->di->getDb();
        $this->shortcodeManager = new ShortcodeManager();
        $cache = $this->getDI()->get('modelsCache');
        $cacheKey = "mysqlVersionSystem";
        $rs = $cache->get($cacheKey);
        if (is_null($rs)) {
            $mySqlVersion = $this->connection->fetchColumn('SELECT version()');
            $mv = explode('-', $mySqlVersion);
            $mver = str_replace('.', '', $mv[0]);
            $cache->save($cacheKey, $mver, 864000);
            $this->mySqlVersion = $mver;
        } else {
            $this->mySqlVersion = $rs;
        }

        $this->applications = \Applicazioni::find([
            'conditions' => 'attivo = 1',
            'cache' => [
                'key' => 'listaAppFlatTbp',
                'lifetime' => 7200
            ]
        ]);
    }

    /**
     * Creazione indice e bulk insert
     * @param \TipologiePost $postType
     * @return array
     */
    public function indexPostType(\TipologiePost $postType){
        try{
            if($postType->attivo == '0') return ['success' => true, 'message' => null];
            foreach($this->applications as $app){

                if(!$this->connection->tableExists('_'.$app->codice.'_'.$postType->slug)){
                    $this->createFlatTable($app, $postType);
                }

                $meta = $this->getPostTypeMeta($postType->id);
                $this->flatMetaFields('_'.$app->codice.'_'.$postType->slug, $meta);

                $filters = $this->getPostTypeFilters($app->id, $postType->id);
                $this->flatFiltersFields('_'.$app->codice.'_'.$postType->slug, $filters);

                $this->bulkPopulateFlatTable('_'.$app->codice.'_'.$postType->slug, $app, $postType);
            }

            return ['success' => true, 'message' => 'complete'];;
        } catch(Exception $e){
            return ['success' => false, 'message' => $e->getMessage()];;
        }
    }

    /**
     * Creazione tabella flat post
     * @param \TipologiePost $postType
     * @throws Exception
     */
    private function createFlatTable(\Applicazioni $applicazione, \TipologiePost $postType){
        $table_name = $applicazione->codice.'_'.$postType->slug;
        $definition = [
            "columns" => [
                new Column(
                    "id",
                    [
                        "type"          => Column::TYPE_INTEGER,
                        "size"          => 11,
                        "notNull"       => true,
                        "unsigned"      => true,
                        "autoIncrement" => true,
                        "primary"       => true
                    ]
                ),
                new Column(
                    "id_post",
                    [
                        "type"          => Column::TYPE_INTEGER,
                        "size"          => 11,
                        "unsigned"      => true,
                        "notNull"       => true
                    ]
                ),
                new Column(
                    "id_tipologia_stato",
                    [
                        "type"          => Column::TYPE_INTEGER,
                        "size"          => 2,
                        "unsigned"      => true,
                        "notNull"       => true
                    ]
                ),
                new Column(
                    "id_users_groups",
                    [
                        "type"          => Column::TYPE_VARCHAR,
                        "size"          => 75,
                        "notNull"       => false
                    ]
                ),
                new Column(
                    "titolo",
                    [
                        "type"    => Column::TYPE_VARCHAR,
                        "size"    => 150,
                        "notNull" => true,
                    ]
                ),
                new Column(
                    "slug",
                    [
                        "type"    => Column::TYPE_VARCHAR,
                        "size"    => 150,
                        "notNull" => true
                    ]
                ),
                new Column(
                    "excerpt",
                    [
                        "type"    => Column::TYPE_VARCHAR,
                        "size"    => 255,
                        "notNull" => true
                    ]
                ),
                new Column(
                    "testo",
                    [
                        "type"    => Column::TYPE_TEXT,
                        "notNull" => true
                    ]
                ),
                new Column(
                    "data_inizio_pubblicazione",
                    [
                        "type"    => Column::TYPE_DATETIME,
                        "notNull" => false
                    ]
                ),
                new Column(
                    "data_fine_pubblicazione",
                    [
                        "type"    => Column::TYPE_DATETIME,
                        "notNull" => false
                    ]
                ),
                new Column(
                    "timestamp",
                    [
                        "type"    => Column::TYPE_DATETIME,
                        "notNull" => true
                    ]
                ),
                new Column(
                    "attivo",
                    [
                        "type"    => Column::TYPE_BOOLEAN,
                        "notNull" => true
                    ]
                )
            ],
            "indexes" => [
                new Index(
                    "id_post",
                    [
                        "id_post"
                    ],
                    'UNIQUE'
                ),
                new Index(
                    "id_tipologia_stato",
                    [
                        "id_tipologia_stato"
                    ]
                ),
                new Index(
                    "slug",
                    [
                        "slug"
                    ]
                ),
                new Index(
                    "attivo",
                    [
                        "attivo"
                    ]
                ),
                new Index(
                    "titolo",
                    [
                        "titolo"
                    ],
                    "FULLTEXT"
                ),
                new Index(
                    "excerpt",
                    [
                        "excerpt"
                    ],
                    "FULLTEXT"
                ),
            ]
        ];

        if($this->mySqlVersion < '5600'){
            $definition["options"] = [
                "ENGINE"          => "MyISAM",
                "TABLE_COLLATION" => "utf8_general_ci",
            ];
        }  else {
            $definition["options"] = [
                "ENGINE"          => "InnoDB",
                "TABLE_COLLATION" => "utf8_general_ci",
            ];
        }

        $create = $this->connection->createTable(
            '_'.$table_name,
            null,
            $definition
        );
        if(!$create){
            throw new Exception( 'Attenzione! Errore nella creazione della tabella flat' );
        }
    }

    /**
     * Estrazione post keys
     * @param $id_post_type
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    private function getPostTypeMeta($id_post_type){
        return \Meta::query()
            ->innerJoin('MetaGroup', 'mg.id = Meta.id_meta_group AND mg.attivo = 1', 'mg')
            ->innerJoin('MetaGroupPostType', 'mgpt.id_meta_group = mg.id AND mgpt.attivo = 1 AND mgpt.id_tipologia_post = "'.$id_post_type.'"', 'mgpt')
            ->where('Meta.attivo = 1')
            ->groupBy('Meta.id')
            ->orderBy('mg.priorita ASC')
            ->execute();
    }

    /**
     * Estrae i meta fields di un determinato post type, prepara le columns convenzionate e aggiunge / rimuove / skippa
     * le colonne sul database a seconda dello stato di allineamento.
     * @param $tableName
     * @param $expectedMetaFields
     * @throws Exception
     */
    private function flatMetaFields($tableName, $expectedMetaFields){
        $metaTable = $tableName.self::meta_suffix;
        if(!$this->connection->tableExists($metaTable)){
            $definition = [
                "columns" => [
                    new Column(
                        "id",
                        [
                            "type"          => Column::TYPE_INTEGER,
                            "size"          => 11,
                            "notNull"       => true,
                            "unsigned"      => true,
                            "autoIncrement" => true,
                            "primary"       => true
                        ]
                    ),
                    new Column(
                        "id_post",
                        [
                            "type"          => Column::TYPE_INTEGER,
                            "size"          => 11,
                            "unsigned"      => true,
                            "notNull"       => true
                        ]
                    )
                ],
                "indexes" => [
                    new Index(
                        "id_post",
                        [
                            "id_post"
                        ],
                        'UNIQUE'
                    )
                ]
            ];
            if($this->mySqlVersion >= '5600'){
                $definition["references"] = [
                    new Reference(
                        $metaTable."_post_fk",
                        [
                            "referencedTable"   => $tableName,
                            "columns"           => ["id_post"],
                            "referencedColumns" => ["id_post"],
                            "onUpdate"          => 'CASCADE',
                            "onDelete"          => 'CASCADE'
                        ]
                    )
                ];
                $definition["options"] = [
                    "ENGINE"          => "InnoDB",
                    "TABLE_COLLATION" => "utf8_general_ci",
                ];
            } else {
                $definition["options"] = [
                    "ENGINE"          => "MyISAM",
                    "TABLE_COLLATION" => "utf8_general_ci",
                ];
            }

            $create = $this->connection->createTable(
                $metaTable,
                null,
                $definition
            );
            if(!$create) throw new Exception( 'Attenzione! Errore nella creazione della tabella meta '.$metaTable );
        }
        $fields = [];

        //Lista delle colonne presenti sulla tabella
        $fieldsList = $this->connection->describeColumns($metaTable);
        $nr = count($fieldsList);
        $columns = [];
        $meta_fields = ['id', 'id_post'];
        //Creo array associativo
        for($i = 0; $i < $nr; $i++){
            $fields[$fieldsList[$i]->getName()] = $fieldsList[$i];
            $columns[] = $fieldsList[$i]->getName();
        }

        /**
         * Creo lo scaffold delle colonne in base ai meta inseriti nel cms
         */
        foreach($expectedMetaFields as $metaField){
            $meta_fields[] = $metaField->key;
            switch($metaField->id_tipologia_meta){
                case 1:
                case 7:
                case 8:
                    //INTERO, CHECKBOX, FILE SINGOLO
                    $columnAttributes = ['type' => Column::TYPE_INTEGER, 'size' => 11, 'notNull' => false ];
                    break;
                case 2:
                    //DECIMALE
                    $columnAttributes = ['type' => Column::TYPE_DECIMAL, 'size' => 14, 'scale' => 8, 'notNull' => false];
                    break;
                case 3:
                case 6:
                    //SELECT
                    //STRINGA
                    $columnAttributes = ['type' => Column::TYPE_VARCHAR, 'size' => 255, 'notNull' => false];
                    break;
                case 4:
                case 9:
                case 10:
                    //TESTO, FILE SINGOLO, FILE COLLECTION, HTML
                    $columnAttributes = ['type' => Column::TYPE_TEXT, 'notNull' => false];
                    break;
                case 5:
                    //DATE/TIME
                    $columnAttributes = ['type' => Column::TYPE_DATETIME, 'notNull' => false];
                    break;
                default:
                    $columnAttributes = ['type' => Column::TYPE_VARCHAR, 'size' => 255, 'notNull' => false];
                    break;
            }

            /**
             * Se la meta_key non corrisponde a una colonna nella tabella, la creo
             */

            if(!array_key_exists($metaField->key, $fields)){
                $addCol = $this->connection->addColumn(
                    $metaTable,
                    null,
                    new Column(
                        $metaField->key,
                        $columnAttributes
                    )
                );
                if(!$addCol)throw new Exception( 'Attenzione! Errore nella aggiunta della colonna '.$metaField->key.' alla tabella meta '.$metaTable );
            } else {
                /**
                 * Se la colonna esiste ma ha una configurazione diversa rispetto allo scaffold, effettuo un alter
                 */
                if($fields[$metaField->key]->getType() !== $columnAttributes['type']){
                    $editCol = $this->connection->modifyColumn(
                        $metaTable,
                        null,
                        new Column(
                            $metaField->key,
                            $columnAttributes
                        )
                    );
                    if(!$editCol)throw new Exception( 'Attenzione! Errore nell\'alter della colonna '.$metaField->key.' alla tabella meta '.$metaTable );
                }
            }

        }
        $diffs = array_diff($columns, $meta_fields);
        if(!empty($diffs)){
            foreach($diffs as $columnDel){
                $delCol = $this->connection->dropColumn(
                    $metaTable,
                    null,
                    $columnDel
                );
                if(!$delCol)throw new Exception( 'Attenzione! Errore nell\'alter della colonna '.$metaField->key.' alla tabella meta '.$metaTable );
            }
        }

        $this->saveColumnMap($metaTable, $meta_fields);

    }

    private function saveColumnMap($table, $columns){
        if (($key = array_search('id', $columns)) !== false) unset($columns[$key]);
        if (($key = array_search('id_post', $columns)) !== false) unset($columns[$key]);
        $columns = array_values($columns);
        $option = \Options::findFirstByOptionName('columns_map'.$table);
        if($option){
            $option->option_value = json_encode($columns);;
            $option->save();
        } else {
            $option = new \Options();
            $option->option_name = 'columns_map'.$table;
            $option->option_value = json_encode($columns);
            $option->save();
        }
    }

    /**
     * Estrazione filters keys
     * @param $id_post_type
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    private function getPostTypeFilters($id_app, $id_post_type){
        return \Filtri::query()
            ->innerJoin('FiltriGroup', 'fg.id = Filtri.id_filtri_group AND fg.attivo = 1', 'fg')
            ->innerJoin('FiltriGroupPostType', 'fgpt.id_filtri_group = fg.id AND fgpt.attivo = 1 AND fgpt.id_tipologia_post = "'.$id_post_type.'"', 'fgpt')
            ->where('Filtri.attivo = 1 AND Filtri.id_applicazione = "'.$id_app.'"')
            ->groupBy('Filtri.id')
            ->orderBy('fg.priorita ASC')
            ->execute();
    }

    /**
     * Estrae i filtri fields di un determinato post type, prepara le columns convenzionate e aggiunge / rimuove / skippa
     * le colonne sul database a seconda dello stato di allineamento.
     *
     * @param $tableName
     * @param $expectedFiltersFields
     * @throws Exception
     */
    private function flatFiltersFields($tableName, $expectedFiltersFields){
        $filtersTable = $tableName.self::filtri_suffix;
        if(!$this->connection->tableExists($filtersTable)){
            $definition = [
                "columns" => [
                    new Column(
                        "id",
                        [
                            "type"          => Column::TYPE_INTEGER,
                            "size"          => 11,
                            "notNull"       => true,
                            "unsigned"      => true,
                            "autoIncrement" => true,
                            "primary"       => true
                        ]
                    ),
                    new Column(
                        "id_post",
                        [
                            "type"          => Column::TYPE_INTEGER,
                            "size"          => 11,
                            "unsigned"      => true,
                            "notNull"       => true
                        ]
                    )
                ],
                "indexes" => [
                    new Index(
                        "id_post",
                        [
                            "id_post"
                        ],
                        'UNIQUE'
                    )
                ],
            ];

            if($this->mySqlVersion >= '5600'){
                $definition["references"] = [
                    new Reference(
                        $filtersTable."_post_fk",
                        [
                            "referencedTable"   => $tableName,
                            "columns"           => ["id_post"],
                            "referencedColumns" => ["id_post"],
                            "onUpdate"          => 'CASCADE',
                            "onDelete"          => 'CASCADE'
                        ]
                    )
                ];
                $definition["options"] = [
                    "ENGINE"          => "InnoDB",
                    "TABLE_COLLATION" => "utf8_general_ci",
                ];
            } else {
                $definition["options"] = [
                    "ENGINE"          => "MyISAM",
                    "TABLE_COLLATION" => "utf8_general_ci",
                ];
            }

            $create = $this->connection->createTable(
                $filtersTable,
                null,
                $definition
            );
            if(!$create) throw new Exception( 'Attenzione! Errore nella creazione della tabella filtri '.$filtersTable );
        }
        $fields = [];

        //Lista delle colonne presenti sulla tabella
        $fieldsList = $this->connection->describeColumns($filtersTable);
        $nr = count($fieldsList);
        $columns = [];
        $filter_fields = ['id', 'id_post'];
        //Creo array associativo
        for($i = 0; $i < $nr; $i++){
            $fields[$fieldsList[$i]->getName()] = $fieldsList[$i];
            $columns[] = $fieldsList[$i]->getName();
        }

        /**
         * Creo lo scaffold delle colonne in base ai meta inseriti nel cms
         */
        foreach($expectedFiltersFields as $filterField){
            $filter_fields[] = $filterField->key;
            $filter_fields[] = 'key_'.$filterField->key;

            $columnAttributes = ['type' => Column::TYPE_VARCHAR, 'size' => 255, 'notNull' => false ];
            $columnAttributesX = ['type' => Column::TYPE_VARCHAR, 'size' => 75, 'notNull' => false ];

            /**
             * Se la meta_key non corrisponde a una colonna nella tabella, la creo
             */
            if(!array_key_exists($filterField->key, $fields)){
                $add = $this->connection->addColumn(
                    $filtersTable,
                    null,
                    new Column(
                        $filterField->key,
                        $columnAttributes
                    )
                );

                $addX = $this->connection->addColumn(
                    $filtersTable,
                    null,
                    new Column(
                        'key_'.$filterField->key,
                        $columnAttributesX
                    )
                );

                if(!$add || !$addX) throw new Exception( 'Attenzione! Errore nella creazione della colonna '.$filterField->key.' nella tabella filtri '.$filtersTable );

                $add_index = $this->connection->addIndex(
                    $filtersTable,
                    null,
                    new Index(
                        'key_'.$filterField->key,
                        [
                            'key_'.$filterField->key
                        ]
                    )
                );
                if(!$add_index) throw new Exception( 'Attenzione! Errore nella creazione della colonna indice '.$filterField->key.' nella tabella filtri '.$filtersTable );
            } else {
                /**
                 * Se la colonna esiste ma ha una configurazione diversa rispetto allo scaffold, effettuo un alter
                 */
                 if($fields[$filterField->key]->getType() !== $columnAttributes['type']){
                     $edit = $this->connection->modifyColumn(
                         $filtersTable,
                         null,
                         new Column(
                             $filterField->key,
                             $columnAttributes
                         )
                     );
                     $editX = $this->connection->modifyColumn(
                         $filtersTable,
                         null,
                         new Column(
                             'key_'.$filterField->key,
                             $columnAttributesX
                         )
                     );
                     if(!$edit || $editX) throw new Exception( 'Attenzione! Errore nell\'alter della colonna indice '.$filterField->key.' nella tabella filtri '.$filtersTable );

                }
            }
        }
        $diffs = array_diff($columns, $filter_fields);
        if(!empty($diffs)){
            foreach($diffs as $columnDel){
                $del = $this->connection->dropColumn(
                    $filtersTable,
                    null,
                    $columnDel
                );
                if(!$del) throw new Exception( 'Attenzione! Errore nella delete della colonna indice '.$filterField->key.' nella tabella filtri '.$filtersTable );
            }
        }
        $this->saveColumnMap($filtersTable, $filter_fields);
    }

    public function bulkPopulateFlatTable($tableName, \Applicazioni $app, \TipologiePost $postType){
        $posts = \Posts::find(
            [
                'conditions' => 'id_tipologia_post = ?1 AND id_applicazione = ?2',
                'bind' => [1 => $postType->id, 2 => $app->id]
            ]
        );
        $metaFieldsKeys = \Options::findFirstByOptionName('columns_map'.$tableName.self::meta_suffix);
        $filtersFieldsKeys = \Options::findFirstByOptionName('columns_map'.$tableName.self::filtri_suffix);

        foreach($posts as $post){
            $this->createFlatRecord($metaFieldsKeys, $filtersFieldsKeys, $post, $tableName);
        }

    }

    private function createFlatRecord(\Options $metaFieldsKeys, \Options $filtersFieldsKeys, \Posts $post, $tableName){
        $rs = $this->connection->fetchOne(
            "
                SELECT
                    id_post
                FROM
                    ".$tableName."
                WHERE
                    id_post = '{$post->id}'
            "
        );
        $id_users_groups = null;
        $ugs = $post->getPostsUsersGroups();
        if($ugs && count($ugs) > 0){
            $id_users_groups = [];
            foreach($ugs as $ug){
                $id_users_groups[] = $ug->id_user_group;
            }
            $id_users_groups = implode(',',$id_users_groups);
        }

        //throw new Exception('cia');
        $flatPostValues = [
            $post->id,
            $post->id_tipologia_stato,
            $id_users_groups,
            self::removeBomUTF8($post->titolo),
            $post->slug,
            self::removeBomUTF8($post->excerpt),
            self::removeBomUTF8($this->shortcodeManager->shortcodify($post->testo, $post->id)),
            $post->data_inizio_pubblicazione,
            $post->data_fine_pubblicazione,
            !is_null($post->data_aggiornamento) ? $post->data_aggiornamento : date('Y-m-d H:i:s'),
            1
        ];

        $flatPostColumns = [
            'id_post',
            'id_tipologia_stato',
            'id_users_groups',
            'titolo',
            'slug',
            'excerpt',
            'testo',
            'data_inizio_pubblicazione',
            'data_fine_pubblicazione',
            'timestamp',
            'attivo'
        ];

        $this->connection->begin();
        if(empty($rs)){

            if(!$this->connection->insert($tableName, $flatPostValues, $flatPostColumns)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            } else {
                $id_post = $post->id;
            }
        } else {
            $id_post = $rs['id_post'];
            if(!$this->connection->update($tableName, $flatPostColumns, $flatPostValues, 'id_post = '.$id_post)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            }
        }

        /**
         * Meta valori
         */
        $metaFieldsColumns = [
            'id_post'
        ];

        $metaFieldsValues = [
            $id_post
        ];

        if(!empty($metaFieldsKeys)){
            $metaFieldsColumns = array_values(array_merge($metaFieldsColumns, json_decode($metaFieldsKeys->option_value, true)));
        }


        $postsMeta = \PostsMeta::find([
            'conditions' => 'post_id = '.$post->id
        ]);
        foreach($postsMeta AS $postMeta){
            if(in_array($postMeta->meta_key, $metaFieldsColumns)){
                $key = array_search($postMeta->meta_key, $metaFieldsColumns);
                $metaFieldsValues[$key] = \PostsMeta::getMetaValue($postMeta);
            }
        }

        foreach($metaFieldsColumns as $index => $val){
            if(!isset($metaFieldsValues[$index])) $metaFieldsValues[$index] = null;
        }
        ksort($metaFieldsValues);
        $rsPm = $this->connection->fetchOne(
            "
                SELECT
                    id_post
                FROM
                    ".$tableName.self::meta_suffix."
                WHERE
                    id_post = '{$post->id}'
            "
        );

        if(empty($rsPm)){
            if(!$this->connection->insert($tableName.self::meta_suffix, $metaFieldsValues, $metaFieldsColumns)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            } else {
                $id_post = $post->id;
            }
        } else {
            $id_post = $rsPm['id_post'];
            if(!$this->connection->update($tableName.self::meta_suffix, $metaFieldsColumns, $metaFieldsValues, 'id_post = '.$id_post)){

                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            }
        }
        /** FINE META VALORI **/

        /**
         * FILTRI
         */
        $filtersFieldsColumns = [
            'id_post'
        ];

        $filtersFieldsValues = [
            $id_post
        ];

        if(!empty($filtersFieldsKeys)){
            $filtersFieldsColumns = array_values(array_merge($filtersFieldsColumns, json_decode($filtersFieldsKeys->option_value, true)));
        }

        $postsFilters = \PostsFiltri::find([
            'conditions' => 'id_post = '.$post->id
        ]);
        foreach($postsFilters AS $postFilter){
            if(in_array($postFilter->Filtri->key, $filtersFieldsColumns)){
                $key = array_search($postFilter->Filtri->key, $filtersFieldsColumns);
                if(!isset($filtersFieldsValues[$key])){
                    $filtersFieldsValues[$key] = self::checkTranslation($postFilter->FiltriValori->valore);
                } else {
                    $filtersFieldsValues[$key].= ','.self::checkTranslation($postFilter->FiltriValori->valore);
                }

                $keyk = array_search('key_'.$postFilter->Filtri->key, $filtersFieldsColumns);
                if(!isset($filtersFieldsValues[$keyk])){
                    $filtersFieldsValues[$keyk] = self::checkTranslation($postFilter->FiltriValori->key);
                } else {
                    $filtersFieldsValues[$keyk].= ','.self::checkTranslation($postFilter->FiltriValori->key);
                }
            }
        }
        foreach($filtersFieldsColumns as $index => $val){
            if(!isset($filtersFieldsValues[$index])) $filtersFieldsValues[$index] = null;
        }
        ksort($filtersFieldsValues);

        $rsFt = $this->connection->fetchOne(
            "
                    SELECT
                        id_post
                    FROM
                        ".$tableName.self::filtri_suffix."
                    WHERE
                        id_post = '{$post->id}'
                "
        );

        if(empty($rsFt)){
            if(!$this->connection->insert($tableName.self::filtri_suffix, $filtersFieldsValues, $filtersFieldsColumns)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            } else {
                $id_post = $post->id;
            }
        } else {
            $id_post = $rsFt['id_post'];
            if(!$this->connection->update($tableName.self::filtri_suffix, $filtersFieldsColumns, $filtersFieldsValues, 'id_post = '.$id_post)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            }
        }
        /** FINE FILTRI VALORI **/

        $this->connection->commit();
    }

    private static function removeBomUTF8($text){
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    private static function checkTranslation($string){
        $translation = \FlatTranslations::findFirst([
            'conditions' => 'original_string = ?1',
            'bind' => [1 => $string],
            'orderby' => 'id DESC',
            'cache' => [
                "key" => "checkTranslationFor".$string,
                "lifetime" => 600
            ]
        ]);
        if($translation){
            return $translation->translation;
        } else {
            return $string;
        }
    }

    /**
     * After Delete
     *
     * @param Event $event
     * @param \TipologiePost $postType
     * @return bool
     */
    public function afterDeletePostEntity(Event $event, \TipologiePost $postType){
        foreach($this->applications as $app){
            if(!$this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::meta_suffix)){
                return true;
            } else {
                if($this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::meta_suffix)) $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
                if($this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix)) $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix);
                $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
            }
            $optionsMeta = \Options::findFirstByOptionName('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
            if($optionsMeta) $optionsMeta->delete();

            $optionsFilters = \Options::findFirstByOptionName('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix);
            if($optionsFilters) $optionsFilters->delete();

            $option = \Options::findFirstByOptionName('reindex_queue');

            if($option) {
                if (!empty($option->option_value)) {
                    $value = json_decode($option->option_value, true);
                    if($key = array_search($postType->id, $value) !== false){
                        unset($value[$key]);
                        $option->option_value = json_encode(array_values($value));
                        $option->save();
                    }
                }
            }
        }
    }

    /**
     * After Delete
     *
     * @param Event $event
     * @param \TipologiePost $postType
     * @return bool
     */
    public function afterDeleteApplication(Event $event, \Applicazioni $app){
        $postTypes = \TipologiePost::find();

        foreach($postTypes as $postType){
            if(!$this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::meta_suffix)){
                return true;
            } else {
                if($this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::meta_suffix)) $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
                if($this->connection->tableExists('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix)) $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix);
                $this->connection->dropTable('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
            }
            $optionsMeta = \Options::findFirstByOptionName('_'.$app->codice.'_'.$postType->slug.self::meta_suffix);
            if($optionsMeta) $optionsMeta->delete();

            $optionsFilters = \Options::findFirstByOptionName('_'.$app->codice.'_'.$postType->slug.self::filtri_suffix);
            if($optionsFilters) $optionsFilters->delete();

            $option = \Options::findFirstByOptionName('reindex_queue');

            if($option) {
                if (!empty($option->option_value)) {
                    $value = json_decode($option->option_value, true);
                    if($key = array_search($postType->id, $value) !== false){
                        unset($value[$key]);
                        $option->option_value = json_encode(array_values($value));
                        $option->save();
                    }
                }
            }
        }
    }

    public function triggerEditSingleEntity(Event $event, \Posts $post){
        $app = $post->Applicazioni;
        $metaFieldsKeys = \Options::findFirstByOptionName('columns_map_'.$app->codice.'_'.$post->TipologiePost->slug.self::meta_suffix);
        $filtersFieldsKeys = \Options::findFirstByOptionName('columns_map_'.$app->codice.'_'.$post->TipologiePost->slug.self::filtri_suffix);

        if($metaFieldsKeys && $filtersFieldsKeys){
            $this->createFlatRecord($metaFieldsKeys, $filtersFieldsKeys, $post, '_'.$app->codice.'_'.$post->TipologiePost->slug);
        }
    }

    public function resetCheckSum(\Posts $post){
        $tipologia_post_slug = $post->TipologiePost->slug;
        $meta = \Meta::findFirst([
            'conditions' => 'key = ?1 AND attivo = 1',
            'bind' => [
                1 => $tipologia_post_slug.'_hash_checksum'
            ],
            'cache' => [
                "key" => $tipologia_post_slug.'_hash_checksum',
                "lifetime" => 2628000
            ]
        ]);

        if($meta){

            $postsMeta = \PostsMeta::findFirst([
                'conditions' => 'id_meta = ?1 AND post_id = ?2',
                'bind' => [
                    1 => $meta->id,
                    2 => $post->id
                ]
            ]);

            if($postsMeta) {
                $postsMeta->setMetaValue($postsMeta, $meta->TipologieMeta->descrizione, '');
                $postsMeta->save();
            }
        }

    }

    public function triggerDeleteSingleEntity(Event $event, \Posts $post){
        $app = $post->Applicazioni;
        if($this->connection->tableExists('_'.$app->codice.'_'.$post->TipologiePost->slug)){
            $this->connection->begin();
            if(!$this->connection->update('_'.$app->codice.'_'.$post->TipologiePost->slug, ['attivo'], ['0'], 'id_post = '.$post->id)){
                $this->connection->rollback();
                throw new Exception($this->connection->getErrorInfo());
            }
            $this->connection->commit();
        }
    }

    /**
     * @param Event $event
     * @param \TipologiePost[] $tipologie_post
     */
    public function afterEditAttribute(Event $event, $tipologie_post){

        $nr = count($tipologie_post);
        $new_values = [];
        for($i = 0; $i < $nr; $i++){
            $new_values[] = $tipologie_post[$i]['id'];
        }

        $option = \Options::findFirstByOptionName('reindex_queue');

        if($option){
            if(!empty($option->option_value)){
                $value = json_decode($option->option_value, true);
                if(is_array($value)){
                    $value = array_values(array_unique(array_merge($value, $new_values)));
                } else {
                    $value = $new_values;
                }
            } else {
                $value = $new_values;
            }
            $option->option_value = json_encode($value);;
            $option->save();
        } else {
            $option = new \Options();
            $option->option_name = 'reindex_queue';
            $option->option_value = json_encode($new_values);
            $option->save();
        }
    }
}