<?php

class ListController extends ControllerBase
{

    /**
     * Limite paginazione di default
     * @var int
     */
    protected $defaultLimit = 10;
    /**
     * Ordinamento di default
     * @var null
     */
    protected $defaultOrderBy = null;
    /**
     * Pagina inziale (usata come variabile di comparazione)
     * @var int
     */
    protected $defaultPage = 1;
    /**
     * Stringa di ricerca di deafault
     * @var null
     */
    protected $defaultSearch = null;
    /**
     * Visualizzazione di default (dipende dal tema)
     * @var string
     */
    protected $defaultListType = 'grid';
    /**
     * Lista dei filtri attivi
     * @var array
     */
    private $listFilters = [];

    /**
     * Lista dei risultati passati alla view
     * @var array
     */
    protected $results = [];

    /**
     * @var string
     */
    protected $prevPageUrl;

    /**
     * @var string
     */
    protected $nextPageUrl;

    /**
     * @see \Phalcon\Mvc\Controller
     */
    public function initialize()
    {
        $this->tag->setTitle('Lista');
        parent::initialize();
    }

    /**
     * Controller di deafault per il listing di tutte le entità facenti parte id un POST_TYPE del cms.
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function listAction()
    {
        /* Recupero lo slug della tipologia di post su cui effettuare il listing. Se la view non esiste torno subito un 404 */
        $post_type = $this->dispatcher->getParam('post_type_slug');
        if (!$this->view->exists($post_type . '/list')) {
            $this->response->redirect('404');
        }
        /* Tabella flat su cui effettuare la query */
        $tableName = '_' . $this->application . '_' . $post_type;

        /* Parametri in ingresso dal dispatcher */
        $filter_params = $this->dispatcher->getParams();

        /* Rimuovo dai parametri che verranno utilizzati per l'elaborazioni di riordino e seo quelli che non mi servono */
        if (array_key_exists('post_type_slug', $filter_params)) unset($filter_params['post_type_slug']);
        if (array_key_exists('application', $filter_params)) unset($filter_params['application']);
        if (array_key_exists('id_application', $filter_params)) unset($filter_params['id_application']);

        $get_params = $this->request->get();

        /**
         * Se ci sono dei filtri nella chiamata di listing corrente
         * allora controllo l'esatta esistenza della relazione con il post type.
         * In caso la verifica fallisca torno un 404
         */
        if (!empty($filter_params)) {
            $filter_params = $this->checkfilterParams($filter_params, $post_type, $this->application);
            if (!$filter_params) {
                return $this->response->redirect('404');
            }
            $this->view->hasFilters = true;
        } else {
            $filter_params = null;
            $this->view->hasFilters = false;

            if ($get_params['_url'] == $this->applicationUrl . $post_type . '/list/') {
                return $this->response->redirect($this->applicationUrl . $post_type . '/');
            }
        }

        /**
         * Analisi degli eventiali parametri presenti in GET per le operazione
         * di Limit, paging, ordering, tipologia listing e ricerca da string
         */
        $this->view->filterParams = $filter_params;
        $this->view->orderBy = $orderBy = isset($get_params['orderby']) ? $get_params['orderby'] : $this->defaultOrderBy;
        $this->view->limit = $limit = isset($get_params['limit']) ? $get_params['limit'] : $this->defaultLimit;
        $this->view->page = $page = isset($get_params['page']) ? $get_params['page'] : $this->defaultPage;
        $this->view->search = $search = isset($get_params['search']) ? $get_params['search'] : $this->defaultSearch;
        $this->view->listType = $listType = isset($get_params['list-type']) ? $get_params['list-type'] : $this->defaultListType;

        if ($search !== $this->defaultSearch) {
            if (empty($search) || strlen($search) < 4) {
                $this->response->redirect('/', 301);
            } else {
                $search = $search;
            }
        }

        /* Se non esiste tabella flat -> 404 */
        if (!$this->connection->tableExists($tableName)) {
            return $this->response->redirect('404');
        } else {
            /**
             * #####################
             * Recupero lista entità
             * #####################
             */
            $rs = $this->readList($post_type, $this->application, $filter_params, $orderBy, $limit, $page, $search);

            //Variabile per view
            if (!empty($rs['nr_rs'])) {
                $this->view->emptyResult = false;
            } else {
                $this->view->emptyResult = true;
            }

            /* Informazione sul post type su cui si effettua il listing */
            $postType = TipologiePost::findFirst([
                'conditions' => 'slug = ?1 AND attivo = 1',
                'bind'       => [1 => $post_type],
                'cache'      => [
                    "key"      => "getPostType" . $post_type . "Detail",
                    "lifetime" => 12000
                ]
            ]);

            /**
             * Se si tratta di una ricerca organizzo la view e i meta in ottica SEO friendly,
             * se non si tratta di una ricerca imposto i meta e gli elementi Seo in modo coerente con i filtri applicati
             */
            if ($search == $this->defaultSearch) {
                $meta_title = $postType->descrizione;
                $meta_description = "Elenco " . $postType->descrizione;

                foreach ($this->listFilters as $filtroValore) {
                    $meta_title .= !is_null($filtroValore->meta_title) && !empty($filtroValore->meta_title) ? ' ' . $filtroValore->meta_title : " - " . $filtroValore->valore;
                    $meta_description .= " - " . $filtroValore->valore;
                }

                $this->tags->setTitle($meta_title);
                $this->tags->setMetaDescription($meta_description . " - " . $this->config->application->appName);

                $canonicalUrl = $this->config->application->protocol.$this->config->application->siteUri.$get_params['_url'];

                if($page > $this->defaultPage){
                    $canonicalUrl.= '?page='.$page;
                }
                $this->tags->setCanonicalUrl($canonicalUrl);

                $this->tags->setOgUrl(\apps\site\library\Cms::getIstance()->getBaseUrl() . $get_params['_url']);
                if (!$this->view->emptyResult) {
                    $this->tags->setRobots('index, follow');
                } else {
                    $this->tags->setRobots('index, follow');
                }
                $this->view->h1 = $meta_title;
            } else {
                $this->tags->setTitle('Ricerca ' . ucfirst($search));
                $this->tags->setMetaDescription('Ricerca ' . ucfirst($search) . " - " . $this->config->application->appName);
                $this->tags->setRobots('noindex, follow');
                $this->view->h1 = 'Ricerca ' . ucfirst($search);
            }

            /**
             * Ottengo lo slug della corrente chiamata di listing da utilizzare per verificare l'esistenza di un template custom
             * Se il template esiste lo utilizzo, altrimenti carico quello di default.
             */
            $listSlug = $this->getListSlugKey($this->dispatcher->getParams());

            if ($this->view->exists($post_type . '/' . $listSlug)) {
                $this->view->pick($post_type . '/' . $listSlug);
            } elseif ($this->view->exists($this->application . '/' . $post_type . '/' . $listSlug)) {
                $this->view->pick($this->application . '/' . $post_type . '/' . $listSlug);
            } elseif ($this->view->exists($this->application . '/' . $post_type . '/list')) {
                $this->view->pick($this->application . '/' . $post_type . '/list');
            } else {
                $this->view->pick($post_type . '/list');
            }

            /* Passaggio variabili alla View e aggiunta assets */
            $this->view->filters = $this->listFilters;
            $this->view->post_type = $postType;
            $this->view->current_url_route = $get_params['_url'];
            $this->view->current_url = $this->currentUrl;
            $this->view->results = $this->results = $rs['rs'];
            $this->view->shown_nr_results = count($rs['rs']);
            $this->view->current_page = $page;
            $this->view->total_results = $rs['nr_rs'];
            $this->view->total_pages = $rs['pages'];
            $this->view->filter_params = $filter_params;

            $this->view->nextPageUrl = $this->nextPageUrl = $page < $rs['pages'] ? $this->getListPageUrl('next', $page, $get_params['_url'], $orderBy, $limit, $search, $listType) : null;
            $this->view->prevPageUrl = $this->prevPageUrl = $page > $this->defaultPage ? $this->getListPageUrl('prev', $page, $get_params['_url'], $orderBy, $limit, $search, $listType) : null;

            if(!is_null($this->nextPageUrl)) $this->tags->addPaginationNextLink($this->config->application->protocol.$this->config->application->siteUri.$this->nextPageUrl);
            if(!is_null($this->prevPageUrl)) $this->tags->addPaginationPrevLink($this->config->application->protocol.$this->config->application->siteUri.$this->prevPageUrl);

            /*$this->view->listUrl = $this->getListPageUrl('list', null, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType);
            $this->view->gridUrl = $this->getListPageUrl('grid', null, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType);*/

            $this->view->pagingUrl = $this->getListPageUrl('paging', null, $get_params['_url'], $orderBy, $limit, $search, $listType);
            $this->view->listUrl = $this->getListPageUrl('list', null, $get_params['_url'], $orderBy, $limit, $search, $listType);
            $this->view->gridUrl = $this->getListPageUrl('grid', null, $get_params['_url'], $orderBy, $limit, $search, $listType);
        }
    }

    /**
     * @param $filters
     * @param $post_type
     * @param $application
     * @return array|bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    private function checkFilterParams($filters, $post_type, $application)
    {
        $nr = count($filters);
        $current_filters = [];
        $return_filters = [];
        $option = Options::findFirst([
            'conditions' => 'option_name = ?1',
            'bind'       => [
                1 => 'columns_map_' . $application . '_' . $post_type . '_filter'
            ],
            'cache'      => [
                "key"      => "optionFilters" . $post_type . $application,
                "lifetime" => 12400
            ]
        ]);

        if ($option && !empty($option)) {
            $flat_filters = json_decode($option->option_value, true);
        } else {
            return false;
        }
        for ($i = 0; $i < $nr; $i++) {
            if (strpos($filters[$i], '-') === -1) return false;

            list($filter_key, $filter_value) = explode('-', $filters[$i], 2);

            $pos = array_search($filter_key, $flat_filters);

            if ($pos === false) return false;

            $current_filters[$pos] = $filters[$i];
            $filtro_valore = FiltriValori::findFirst([
                'conditions' => 'key = ?1',
                'bind'       => [
                    1 => $filter_value
                ],
                'cache'      => [
                    "key"      => "checkFilterValueExistance" . $filter_key . $filter_value,
                    "lifetime" => 12400
                ]
            ]);

            if (!$filtro_valore) return false;
            $return_filters[$filter_key] = $filter_value;
            $this->listFilters[] = $filtro_valore;
        }

        //Controllo ordinamento array
        ksort($current_filters);
        if (array_values($current_filters) !== $filters) {
            $this->response->redirect('/' . $post_type . '/list/' . implode('/', $current_filters), false, 301);
            return $this->response;
        }
        return $return_filters;
    }

    /**
     * Compilatore delle query di listing / ricerca su tabelle flat generate dal cms
     *
     * @param $post_type_slug - slug della tipologia post
     * @param $application - id applicazione
     * @param null $filters - filtri in entrata da dispatcher
     * @param null $orderBy
     * @param int $limit
     * @param int $page
     * @param null $search - query di ricerca
     * @return array
     */
    private function readList($post_type_slug, $application, $filters = null, $orderBy = null, $limit = 10, $page = 1, $search = null)
    {
        $postTypeMetaFields = self::getPostTypeMetaFields($post_type_slug);
        $postTypeFilterFields = self::getPostTypeFilterFields($post_type_slug);
        $is_search = !is_null($search) && !empty($search) && strlen($search) > 3;
        $columns_select = [];
        $nr = count($postTypeMetaFields);
        $matches_conditions = [];
        $orderby_search = [];

        if ($is_search) {
            $search = trim(str_replace(['+', '*', '-', "'", '\/'], ' ', $this->db->escapeString($search)));
            $matches_conditions[] = "MATCH(e.titolo) AGAINST ('{$search}' IN BOOLEAN MODE)";
            $matches_conditions[] = "MATCH(e.excerpt) AGAINST ('{$search}' IN BOOLEAN MODE)";
            $columns_select[] = "MATCH(e.titolo) AGAINST('{$search}') AS punteggio_titolo";
            $columns_select[] = "MATCH(e.excerpt) AGAINST('{$search}') AS punteggio_excerpt";
            $orderby_search[] = "punteggio_titolo DESC";
            $orderby_search[] = "punteggio_excerpt DESC";
        }

        $n = count($postTypeFilterFields);
        for ($x = 0; $x < $n; $x++) {
            $columns_select[] = "ef." . $postTypeFilterFields[$x] . " AS filter_" . $postTypeFilterFields[$x];
        }

        for ($i = 0; $i < $nr; $i++) {
            $columns_select[] = "em." . $postTypeMetaFields[$i] . " AS meta_" . $postTypeMetaFields[$i];
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS
                  e.*,
                  " . implode(',' . PHP_EOL, $columns_select) . "
                FROM
                  _" . $application . "_" . $post_type_slug . " e
                INNER JOIN  _" . $application . "_" . $post_type_slug . "_meta em ON em.id_post = e.id_post
                INNER JOIN  _" . $application . "_" . $post_type_slug . "_filter ef ON ef.id_post = e.id_post
                WHERE
                    e.id_tipologia_stato = 1
                AND
                    e.attivo = 1 
                AND
                    e.data_inizio_pubblicazione < NOW()
            ";

        if (!is_null($filters)) {
            foreach ($filters as $column => $value) {
                $query .= " AND (ef.key_" . $column . " = '{$value}' OR FIND_IN_SET('{$value}', ef.key_" . $column . ") > 0) ";
            }
        }

        if (!is_null($search) && !empty($search) && strlen($search) > 3) {
            $query .= " AND ( " . implode(' OR ' . PHP_EOL, $matches_conditions) . " ) ";
        }

        if (!$this->isUserLoggedIn) {
            $query .= " AND e.id_users_groups IS NULL ";
        } else {
            $query .= " AND (e.id_users_groups IS NULL OR FIND_IN_SET('" . $this->user['id_users_groups'] . "', e.id_users_groups) > 0)";
        }

        if (is_null($orderBy)) {
            if (!$is_search) {
                $query .= " ORDER BY e.data_inizio_pubblicazione DESC, e.id DESC ";
            } else {
                $query .= " ORDER BY " . implode(',' . PHP_EOL, $orderby_search) . " ";
            }
        } else {
            switch ($orderBy) {
                case 'titolo':
                    $query .= " ,e.titolo ASC ";
                    break;
                case 'titolo-za':
                    $query .= " ,e.titolo DESC ";
                    break;
                default:
                    $query .= " ,e.data_inizio_pubblicazione DESC, e.id DESC ";
                    break;
            }
        }
        if (!is_null($limit)) {
            $offset = ($page == 1) ? 0 : ($page - 1) * $limit;
            $query .= " LIMIT " . $offset . ", " . $limit;
        }
        $q = $this->connection->query($query);
        $q->setFetchMode(Phalcon\Db::FETCH_OBJ);
        $results = $q->fetchAll();
        $foundResults = $this->connection->query("SELECT FOUND_ROWS() as nr")->fetch();
        $pages = !is_null($limit) ? ceil($foundResults['nr'] / $limit) : 1;

        if (!empty($results)) {
            $nr = count($results);
            for ($i = 0; $i < $nr; $i++) {
                if (!is_null($results[$i]->meta_immagine)) {
                    $results[$i]->file = Files::findFirst([
                        'conditions' => 'id = ?1',
                        'bind'       => [1 => $results[$i]->meta_immagine],
                        'cache'      => [
                            "key"      => "listFindFile" . $results[$i]->meta_immagine,
                            "lifetime" => 12400
                        ]
                    ]);

                }
                $results[$i]->readLink = $application == $this->config->application->defaultCode ?
                    DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $results[$i]->slug :
                    DIRECTORY_SEPARATOR . $application . DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $results[$i]->slug;
            }
        }

        return [
            'rs'    => $results,
            'nr_rs' => $foundResults['nr'],
            'pages' => $pages
        ];
    }

    /**
     * Dal dispatcher escludo il post type e estraggo i parametri in entrata,
     * il metodo serve a ritornare uno slug di listing unico utile per effettuare il pick di eventuali
     * view personalizzate. In caso il dispatching riguard la lista totale delle entità di un post type
     * la stringa ritornata sarà 'list-root'
     *
     * @param $dispatcher
     * @return string
     */
    private static function getListSlugKey($dispatcher)
    {
        unset($dispatcher['post_type_slug']);
        if (empty($dispatcher)) {
            $dispatcher[] = 'list-root';
        }
        return implode('-', $dispatcher);
    }

    private function getListPageUrl($action, $current_page, $base_url, $orderBy, $limit, $search, $listType)
    {
        $return = $base_url;
        $sign = '?';
        switch ($action) {
            case 'next':
                if ($orderBy !== $this->defaultOrderBy) {
                    $return .= $sign . 'orderby=' . $orderBy;
                    $sign = '&';
                }
                if ($limit !== $this->defaultLimit) {
                    $return .= $sign . 'limit=' . $limit;
                    $sign = '&';
                }

                if ($search !== $this->defaultSearch) {
                    $return .= $sign . 'search=' . $search;
                    $sign = '&';
                }
                if ($listType !== $this->defaultListType) {
                    $return .= $sign . 'list-type=' . $listType;
                    $sign = '&';
                }
                $return .= $sign . 'page=' . ($current_page + 1);
                break;
            case 'prev':
                if ($orderBy !== $this->defaultOrderBy) {
                    $return .= $sign . 'orderby=' . $orderBy;
                    $sign = '&';
                }
                if ($limit !== $this->defaultLimit) {
                    $return .= $sign . 'limit=' . $limit;
                    $sign = '&';
                }

                if ($search !== $this->defaultSearch) {
                    $return .= $sign . 'search=' . $search;
                    $sign = '&';
                }
                if ($listType !== $this->defaultListType) {
                    $return .= $sign . 'list-type=' . $listType;
                    $sign = '&';
                }
                $return .= $sign . 'page=' . ($current_page - 1);
                break;
            case 'paging':
                if ($orderBy !== $this->defaultOrderBy) {
                    $return .= $sign . 'orderby=' . $orderBy;
                    $sign = '&';
                }
                if ($limit !== $this->defaultLimit) {
                    $return .= $sign . 'limit=' . $limit;
                    $sign = '&';
                }

                if ($search !== $this->defaultSearch) {
                    $return .= $sign . 'search=' . $search;
                    $sign = '&';
                }
                if ($listType !== $this->defaultListType) {
                    $return .= $sign . 'list-type=' . $listType;
                    $sign = '&';
                }
                $return .= $sign . 'page=';
                break;
                if ($orderBy !== $this->defaultOrderBy) {
                    $return .= $sign . 'orderby=' . $orderBy;
                    $sign = '&';
                }
                if ($limit !== $this->defaultLimit) {
                    $return .= $sign . 'limit=' . $limit;
                    $sign = '&';
                }

                if ($search !== $this->defaultSearch) {
                    $return .= $sign . 'search=' . $search;
                    $sign = '&';
                }
                $return .= $sign . 'list-type=list';
                break;
            case 'grid':
                if ($orderBy !== $this->defaultOrderBy) {
                    $return .= $sign . 'orderby=' . $orderBy;
                    $sign = '&';
                }
                if ($limit !== $this->defaultLimit) {
                    $return .= $sign . 'limit=' . $limit;
                    $sign = '&';
                }

                if ($search !== $this->defaultSearch) {
                    $return .= $sign . 'search=' . $search;
                    $sign = '&';
                }
                //$return.= $sign.'list-type=grid';
                break;
        }
        return $return;
    }

    /**
     * Controller di deafault per il listing di tutte le entità facenti parte id un TAG del cms.
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function tagAction()
    {

        /* Recupero lo slug della tipologia di post su cui effettuare il listing. Se la view non esiste torno subito un 404 */
        $post_type = $this->dispatcher->getParam('post_type_slug');

        /* Tabella flat su cui effettuare la query */
        $tableName = '_' . $this->application . '_' . $post_type;

        /* Parametri in ingresso dal dispatcher */
        $tag_params = $this->dispatcher->getParams();

        /* Rimuovo dai parametri che verranno utilizzati per l'elaborazioni di riordino e seo quelli che non mi servono */
        if (array_key_exists('post_type_slug', $tag_params)) unset($tag_params['post_type_slug']);
        if (array_key_exists('application', $tag_params)) unset($tag_params['application']);
        /**
         * Se ci sono dei filtri nella chiamata di listing DEL TAG corrente
         * allora controllo l'esistenza di almeno 1 correlazione con il post_type.
         * In caso la verifica fallisca torno un 404
         */
        if (!empty($tag_params)) {
            $tag = $tag_params[0];
            $tag = $this->checkTag($tag, $post_type);
            if (!$tag) {
                return $this->response->redirect($this->applicationUrl . '404');
            }
        } else {
            $this->response->redirect($this->applicationUrl . '404');
        }

        /**
         * Analisi degli eventiali parametri presenti in GET per le operazione
         * di Limit, paging, ordering, tipologia listing e ricerca da string
         */
        $get_params = $this->request->get();
        $this->view->orderBy = $orderBy = isset($get_params['orderby']) ? $get_params['orderby'] : $this->defaultOrderBy;
        $this->view->limit = $limit = isset($get_params['limit']) ? $get_params['limit'] : $this->defaultLimit;
        $this->view->page = $page = isset($get_params['page']) ? $get_params['page'] : $this->defaultPage;
        $this->view->listType = $listType = isset($get_params['list-type']) ? $get_params['list-type'] : $this->defaultListType;

        if (!$this->connection->tableExists($tableName)) {
            return $this->response->redirect('404');
        } else {
            /**
             * Get lista post
             */
            $rs = $this->readTagList($post_type, $this->application, $tag->tag, $orderBy, $limit, $page);

            if (!empty($rs['nr_rs'])) {
                $this->view->emptyResult = false;
            } else {
                $this->view->emptyResult = true;
            }

            $postType = TipologiePost::findFirst([
                'conditions' => 'slug = ?1 AND attivo = 1',
                'bind'       => [1 => $post_type],
                'cache'      => [
                    "key"      => "getPostType" . $post_type . "Detail",
                    "lifetime" => 12000
                ]
            ]);

            $meta_title = $postType->descrizione . ' - ' . $tag->titolo;
            $meta_description = "Elenco " . $postType->descrizione . " - " . $tag->titolo;

            /**
             * Set Meta informazioni
             */
            $this->tags->setTitle($meta_title);
            $this->tags->setMetaDescription($meta_description . " - " . $this->config->application->appName);
            $this->tags->setCanonicalUrl($this->config->application->protocol . $this->config->application->siteUri . $get_params['_url']);
            $this->tags->setOgUrl($this->config->application->protocol . $this->config->application->siteUri . $get_params['_url']);
            $this->tags->setRobots('index, follow');
            $this->view->h1 = $meta_title;

            /**
             * Setto le view Vars
             */
            $this->view->pick($post_type . '/tag');
            $this->view->post_type = $postType;
            $this->view->current_tag = $tag;
            $this->view->current_url_route = $get_params['_url'];
            $this->view->current_url = $this->currentUrl;
            $this->view->results = $rs['rs'];
            $this->view->current_page = $page;
            $this->view->total_results = $rs['nr_rs'];
            $this->view->total_pages = $rs['pages'];

            $this->view->nextPageUrl = $page < $rs['pages'] ? $this->getListPageUrl('next', $page, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType) : null;
            $this->view->prevPageUrl = $page > $this->defaultPage ? $this->getListPageUrl('prev', $page, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType) : null;
            $this->view->pagingUrl = $this->getListPageUrl('paging', null, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType);
            /*$this->view->listUrl = $this->getListPageUrl('list', null, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType);
            $this->view->gridUrl = $this->getListPageUrl('grid', null, $get_params['_url'], $orderBy, $limit, $this->defaultSearch, $listType);*/
            $this->addLibraryAssets(['lazyLoad'], $postType->slug . '-listing-list');
        }
    }

    /**
     * @param $tag
     * @param $post_type
     * @return bool|\Phalcon\Mvc\Model\ResultsetInterface
     */
    private function checkTag($tag, $post_type)
    {
        $tag = Tags::query()
            ->innerJoin('PostsTags', 'pt.id_tag = Tags.id AND pt.attivo = 1', 'pt')
            ->innerJoin('Posts', 'p.id = pt.id_post AND p.id_tipologia_stato = 1 AND p.attivo = 1', 'p')
            ->innerJoin('TipologiePost', 'tp.id = p.id_tipologia_post AND tp.attivo = 1 AND tp.slug = ?2', 'tp')
            ->where('Tags.tag = ?1 AND Tags.attivo = 1 AND Tags.id_applicazione = ?3')
            ->bind([
                1 => $tag,
                2 => $post_type,
                3 => $this->id_application
            ])
            ->cache([
                "key"      => "checkTag" . $tag . $post_type . $this->application,
                "lifetime" => 12400
            ])
            ->execute()->getFirst();
        if (!$tag) return false;
        return $tag;
    }

    private function readTagList($post_type_slug, $application, $tag, $orderBy = null, $limit = 10, $page = 1)
    {

        $postTypeMetaFields = $this->getPostTypeMetaFields($post_type_slug);
        $postTypeFilterFields = $this->getPostTypeFilterFields($post_type_slug);
        $columns_select = [];

        $nr = count($postTypeMetaFields);
        $n = count($postTypeFilterFields);

        for ($x = 0; $x < $n; $x++) {
            $columns_select[] = "ef." . $postTypeFilterFields[$x] . " AS filter_" . $postTypeFilterFields[$x];
        }

        for ($i = 0; $i < $nr; $i++) {
            $columns_select[] = "em." . $postTypeMetaFields[$i] . " AS meta_" . $postTypeMetaFields[$i];
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS
                  e.*,
                  f.*,
                  " . implode(',' . PHP_EOL, $columns_select) . "
                FROM
                  _" . $application . "_" . $post_type_slug . " e
                INNER JOIN  _" . $application . "_" . $post_type_slug . "_meta em ON em.id_post = e.id_post
                INNER JOIN  _" . $application . "_" . $post_type_slug . "_filter ef ON ef.id_post = e.id_post
                INNER JOIN files f ON f.id = em.immagine AND f.attivo = 1
                INNER JOIN posts_tags pt ON pt.id_post = e.id_post AND pt.attivo = 1
                INNER JOIN tags t ON t.id = pt.id_tag AND t.attivo = 1
                WHERE
                    t.tag = '{$tag}'
                AND
                    e.id_tipologia_stato = 1
                AND
                    e.data_inizio_pubblicazione < NOW()
            ";
        if (!$this->isUserLoggedIn) {
            $query .= " AND e.id_users_groups IS NULL ";
        } else {
            $query .= " AND (e.id_users_groups IS NULL OR FIND_IN_SET('" . $this->user['id_users_groups'] . "', e.id_users_groups) > 0)";
        }

        if (is_null($orderBy)) {
            $query .= " ORDER BY e.data_inizio_pubblicazione DESC, e.id DESC ";
        } else {
            switch ($orderBy) {
                case 'titolo':
                    $query .= "
                       ,e.titolo ASC
                    ";
                    break;
                case 'titolo-za':
                    $query .= "
                       e.titolo DESC
                    ";
                    break;
                default:
                    $query .= "
                       ,e.data_inizio_pubblicazione DESC, e.id DESC
                    ";
                    break;
            }
        }

        if (!is_null($limit)) {
            $offset = ($page == 1) ? 0 : ($page - 1) * $limit;
            $query .= " LIMIT " . $offset . ", " . $limit;
        }
        $q = $this->connection->query($query);
        $q->setFetchMode(Phalcon\Db::FETCH_OBJ);
        $results = $q->fetchAll();
        $foundResults = $this->connection->query("SELECT FOUND_ROWS() as nr")->fetch();
        $pages = !is_null($limit) ? ceil($foundResults['nr'] / $limit) : 1;
        if (!empty($results)) {
            $nr = count($results);
            for ($i = 0; $i < $nr; $i++) {
                if (!is_null($results[$i]->meta_immagine)) {
                    $results[$i]->file = Files::findFirst([
                        'conditions' => 'id = ?1',
                        'bind'       => [1 => $results[$i]->meta_immagine],
                        'cache'      => [
                            "key"      => "listFindFile" . $results[$i]->meta_immagine,
                            "lifetime" => 12400
                        ]
                    ]);

                    $results[$i]->readLink = $application == $this->config->application->defaultCode ?
                        DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $results[$i]->slug :
                        DIRECTORY_SEPARATOR . $application . DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $results[$i]->slug;
                }
            }
        }
        return [
            'rs'    => $results,
            'nr_rs' => $foundResults['nr'],
            'pages' => $pages
        ];
    }

}