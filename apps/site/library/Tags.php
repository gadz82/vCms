<?php
namespace apps\site\library;

use apps\site\library\helpers\StructuredDataHelper;
use Phalcon\Db;
use Phalcon\Di;
use Phalcon\Tag;

/**
 * Class Tags
 * @package apps\site\library
 */
class Tags extends Tag
{
    /**
     * @var string
     */
    private $meta_description;

    /**
     * @var string
     */
    private $og_title;

    /**
     * @var string
     */
    private $og_description;

    /**
     * @var string
     */
    private $og_url;

    /**
     * @var string
     */
    private $og_image;

    /**
     * @var string
     */
    private $og_video;

    /**
     * @var string
     */
    private $canonical_url;

    /**
     * @var string
     */
    private $robots;

    /**
     * @var string
     */
    private $additional_heading;

    /**
     * @var string
     */
    private $required_js = [];

    /**
     * @var string
     */
    private $required_css = [];

    /**
     * @var string
     */
    private $paginationNext;

    /**
     * @var string
     */
    private $paginationPrev;

    /**
     * @param $block_key
     * @param bool $include_tags
     * @return bool
     */
    public static function renderBlock($block_key, $include_tags = true)
    {
        $code_block = \Blocks::findFirst([
            'conditions' => 'key = ?1 AND id_applicazione = ?2',
            'bind'       => [1 => $block_key, 2 => \apps\site\library\Cms::getIstance()->id_application],
            'cache'      => [
                "key"      => "BlocksfindFirstByKey" . $block_key . \apps\site\library\Cms::getIstance()->id_application,
                "lifetime" => 12400
            ]
        ]);
        if (!$code_block) return false;
        $return = "";
        if ($code_block) {
            if ($include_tags) {
                switch ($code_block->id_tipologia_block) {
                    /* HTML */
                    case 1:
                        $return = $code_block->content;
                        break;
                    /* CSS */
                    case 2:
                        $return = '<style type="text/css">'
                            . $code_block->content .
                            '</style>';
                        break;
                    /* JAVASCRIPT */
                    case 3:
                        $return = '<script type="text/javascript">'
                            . $code_block->content .
                            '</script>';
                        break;
                    default:
                        $return = $code_block->content;
                        break;
                }
            } else {
                $return = $code_block->content;
            }
        }
        return Di::getDefault()->get('shortcodes')->shortcodify($return);

    }

    /**
     * @param $block_key
     * @return bool
     */
    public function blockExists($block_key)
    {

        $code_block = \Blocks::findFirst([
            'conditions' => 'key = ?1 AND id_applicazione = ?2',
            'bind'       => [1 => $block_key, 2 => \apps\site\library\Cms::getIstance()->id_application],
            'cache'      => [
                "key"      => "BlocksfindFirstByKey" . $block_key . \apps\site\library\Cms::getIstance()->id_application,
                "lifetime" => 56000
            ]
        ]);
        if (!$code_block) return false;
        return true;
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getOgTitle($tag = true)
    {
        if (!empty($this->og_title)) {
            $content = $this->og_title;
        } else {
            $content = strip_tags(parent::getTitle());
        }
        return $tag ? parent::tagHtml('meta', ['property' => 'og:title', 'content' => str_replace('&', '-', $content)]) : $content;
    }

    /**
     * @param $og_title
     */
    public function setOgTitle($og_title)
    {
        $this->og_title = $og_title;
    }

    /**
     * @param $collection_name
     * @return string
     */
    public function outputCssInline($collection_name)
    {
        $assets = Di::getDefault()->get('assets');
        if ($assets->exists($collection_name)) {
            $collection = $assets->collection($collection_name);
            if (!file_exists($collection->getTargetPath())) $assets->outputCss($collection_name);
            $content = file_get_contents($collection->getTargetPath());
            return parent::tagHtml('style', ['type' => 'text/css']) . $content . parent::tagHtmlClose('style', true);
        }
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getOgDescription($tag = true)
    {
        if (!empty($this->og_description)) {
            $content = $this->og_description;
        } else {
            $content = $this->getMetaDescription(false);
        }
        return $tag ? parent::tagHtml('meta', ['property' => 'og:description', 'content' => str_replace('&', '-', $content)]) : $content;
    }

    /**
     * @param $og_desc
     */
    public function setOgDescription($og_desc)
    {
        $this->og_description = $og_desc;
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getMetaDescription($tag = true)
    {
        if (!empty($this->meta_description)) {
            $content = $this->meta_description;
        } else {
            $view = $this->getDi()->get('view');
            $default_meta_desc = \Options::findFirst([
                'conditions' => "option_name = 'default_meta_description' AND attivo = 1",
                'cache'      => [
                    "key"      => "getDefaultMetaDescription",
                    "lifetime" => 12400
                ]
            ]);
            $content = (isset($view->meta_description) && !empty($view->meta_description)) ?
                $view->meta_description :
                ($default_meta_desc ? $default_meta_desc->option_value : strip_tags(parent::getTitle()));
        }

        return $tag ? parent::tagHtml('meta', ['name' => 'description', 'content' => $content]) : $content;
    }

    /**
     * @param $meta_description
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getOgUrl($tag = true)
    {
        if (!empty($this->og_url)) {
            $content = $this->og_url;
        } else {
            $content = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        return $tag ? parent::tagHtml('meta', ['property' => 'og:url', 'content' => $content]) : $content;
    }

    /**
     * @param $og_url
     */
    public function setOgUrl($og_url)
    {
        $this->og_url = $og_url;
    }

    /**
     * @param bool $tag
     * @return bool|string
     */
    public function getOgImage($tag = true)
    {
        if (!empty($this->og_image)) {
            $content = $this->og_image;
            return $tag ? parent::tagHtml('meta', ['property' => 'og:image', 'content' => $content]) : $content;
        } else {
            return false;
        }
    }

    /**
     * @param $og_image
     */
    public function setOgImage($og_image)
    {
        $this->og_image = $og_image;
    }

    /**
     * @param bool $tag
     * @return bool|string
     */
    public function getOgVideo($tag = true)
    {
        if (!empty($this->og_video)) {
            $content = $this->og_video;
            return $tag ? parent::tagHtml('meta', ['property' => 'og:video', 'content' => $content]) : $content;
        } else {
            return false;
        }
    }

    /**
     * @param $video
     */
    public function setOgVideo($video)
    {
        $this->og_video = $video;
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getCanonicalUrl($tag = true)
    {
        $content = !empty($this->canonical_url) ? $this->canonical_url : Cms::getIstance()->getConfig()->application->protocol . "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $tag ? parent::tagHtml('link', ['rel' => 'canonical', 'href' => $content]) : $content;
    }

    /**
     * @param $link
     */
    public function setCanonicalUrl($link)
    {
        $this->canonical_url = $link;
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getRobots($tag = true)
    {
        $content = !empty($this->robots) ? $this->robots : 'index/follow';
        return $tag ? parent::tagHtml('meta', ['name' => 'robots', 'cotent' => $content]) : $content;
    }

    /**
     * @param $robots
     */
    public function setRobots($robots)
    {
        $this->robots = $robots;
    }

    /**
     * @return string
     */
    public function getAdditionalHeading(){
        if(!empty($this->additional_heading) && $this->additional_heading != strip_tags($this->additional_heading)){
            return $this->additional_heading;
        }
    }

    /**
     * @param $additionalHeading
     */
    public function setAdditionalHeading($additionalHeading){
        $this->additional_heading = $additionalHeading;
    }

    /**
     * @param $resource
     */
    public function injectJsFromDi($resource)
    {
        if (is_array($resource)) {
            $nr = count($resource);
            for ($i = 0; $i < $nr; $i++) {
                $this->required_js[] = $resource[$i];
            }
        } else {
            $this->required_js[] = $resource;
        }
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getInjectedJsByDi($tag = true)
    {
        $nr = count($this->required_js);
        $return = "";
        for ($i = 0; $i < $nr; $i++) {
            $return .= parent::tagHtml('script', ['type' => 'text/javascript', 'src' => $this->required_js[$i]]) . parent::tagHtmlClose('script');
        }

        return $tag ? $return : $this->required_js;
    }

    /**
     * @param $resource
     */
    public function injectCssFromDi($resource)
    {
        if (is_array($resource)) {
            $nr = count($resource);
            for ($i = 0; $i < $nr; $i++) {
                $this->required_css[] = $resource[$i];
            }
        } else {
            $this->required_css[] = $resource;
        }
    }

    /**
     * @param bool $tag
     * @return string
     */
    public function getInjectedCssByDi($tag = true)
    {
        $nr = count($this->required_css);
        $return = "";
        for ($i = 0; $i < $nr; $i++) {
            $return .= parent::tagHtml('link', ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $this->required_css[$i]], true);
        }
        return $tag ? $return : $this->required_css;
    }

    /**
     * @return mixed
     */
    public function getStructs()
    {
        return StructuredDataHelper::getIstance()->getStructs();
    }

    /**
     * @param $key
     * @param $val
     */
    public function addStruct($key, $val)
    {
        StructuredDataHelper::getIstance()->addStruct($key, $val);
    }

    /**
     * @param $url
     */
    public function addPaginationPrevLink($url)
    {
        $this->paginationPrev = $url;
    }

    /**
     * @param $url
     */
    public function addPaginationNextLink($url)
    {
        $this->paginationNext = $url;
    }

    /**
     * @return string
     */
    public function getPaginationLinks()
    {
        $return = "";
        if (!empty($this->paginationPrev)) {
            $return .= parent::tagHtml('link', ['rel' => 'prev', 'href' => $this->paginationPrev]) . PHP_EOL;
        }
        if (!empty($this->paginationNext)) {
            $return .= parent::tagHtml('link', ['rel' => 'next', 'href' => $this->paginationNext]) . PHP_EOL;
        }
        return $return;
    }

    /**
     * @param $form_key
     * @param int $id_post
     * @param array $data
     * @return bool
     */
    public function renderForm($form_key, $id_post = 1, $data = [])
    {
        $form = Forms::getForm($form_key, $id_post, $this);
        if (!$form) return false;
        $view = $this->getDi()->get('view');
        $form_template = $view->exists('partials/forms/' . $form_key) ? $form_key : 'form';
        if (empty($data)) $data['content_ids'] = "['" . $id_post . "']";
        $rs = $view->getRender('forms', $form_template, ['form' => $form['form'], 'formEntity' => $form['formEntity'], 'data' => $data], function ($view) {
            $view->setViewsDir("../apps/site/views/partials/");
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
        return $rs;
    }

    /**
     * @param $post_type
     * @param $filter_key
     * @param $filter_params
     * @return bool
     */
    public function renderFilterValuesMenu($post_type, $filter_key, $filter_params)
    {
        $current_filter_value = !is_null($filter_params) && array_key_exists($filter_key, $filter_params) ? $filter_params[$filter_key] : null;
        $baseUri = Cms::getIstance()->getApplicationUrl(null, true) . $post_type;
        if (!empty($filter_params)) {
            $uriComponents = [];
            foreach ($filter_params as $k => $v) {
                if ($k !== $filter_key) {
                    $uriComponents[] = $k . '-' . $v;
                }
            }
            $baseUri .= !empty($uriComponents) ? implode('/', $uriComponents) . '/' : '';
        }

        $filtro = \Filtri::findFirst([
            'conditions' => 'key = ?1 AND id_applicazione = ?2',
            'bind'       => [1 => $filter_key, 2 => \apps\site\library\Cms::getIstance()->id_application],
            'cache'      => [
                'key'      => 'dettaglio.filtro.' . $filter_key . \apps\site\library\Cms::getIstance()->id_application,
                "lifetime" => 56400
            ]
        ]);
        if (!$filtro) return false;

        $valori = \FiltriValori::find([
            'columns'    => 'FiltriValori.id,
                FiltriValori.valore AS titolo_valore, 
                FiltriValori.key AS key_filtro_valore,
                f.key AS key_filtro, 
                FiltriValori.id_filtro_valore_parent',
            'conditions' => 'f.id = ?1 AND FiltriValori.attivo = 1',
            'bind'       => [1 => $filtro->id],
            'joins'      => [
                ['Filtri', 'f.id = FiltriValori.id_filtro AND f.attivo = 1', 'f', 'INNER'],
                ['PostsFiltri', 'pf.id_filtro_valore = FiltriValori.id AND pf.attivo = 1', 'pf', 'INNER'],
                ['Posts', 'p.id = pf.id_post AND p.id_tipologia_stato = 1 AND p.attivo = 1', 'p', 'INNER']
            ],
            'group'      => 'FiltriValori.id',
            'cache'      => [
                'key'      => 'dettaglio.filtrivalori.' . $filtro->id . \apps\site\library\Cms::getIstance()->id_application,
                "lifetime" => 56400
            ]
        ])->toArray();

        if (!$valori) return false;
        $values = [];
        $i = 0;
        while (!empty($valori)) {
            if (!isset($valori[$i]) && $i > 0) $i = 0;

            if (is_null($valori[$i]['id_filtro_valore_parent'])) {
                $values[$valori[$i]['id']] = $valori[$i];

                if (!is_null($current_filter_value) && $current_filter_value == $valori[$i]['key_filtro_valore']) $values[$valori[$i]['id']]['active'] = true;

                unset($valori[$i]);
            } else {
                if (isset($values[$valori[$i]['id_filtro_valore_parent']])) {
                    if (!isset($values[$valori[$i]['id_filtro_valore_parent']]['childrens'])) {
                        $values[$valori[$i]['id_filtro_valore_parent']]['childrens'] = [];
                    }
                    $values[$valori[$i]['id_filtro_valore_parent']]['childrens'][$valori[$i]['id']] = $valori[$i];
                    if (!is_null($current_filter_value) && $current_filter_value == $valori[$i]['key_filtro_valore']) $values[$valori[$i]['id_filtro_valore_parent']]['childrens'][$valori[$i]['id']]['active'] = true;
                    unset($valori[$i]);
                }
            }
            $i++;
        }
        $active_widget_show_all = $current_filter_value !== null;

        $view = $this->getDi()->get('view');
        $rs = $view->getRender($post_type, $filter_key, ['filtro' => $filtro, 'filtri_valori' => $values, 'baseUri' => $baseUri, 'active_widget_show_all' => $active_widget_show_all], function ($view) {
            $view->setViewsDir("../apps/site/views/partials/menu/");
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
        return $rs;
    }

    /**
     * @param $post_type
     * @param $filter_key
     * @param $filter_params
     * @return bool
     */
    public function renderSubFilterValuesMenu($post_type, $filter_key, $filter_params)
    {
        $current_filter_value = !is_null($filter_params) && array_key_exists($filter_key, $filter_params) ? $filter_params[$filter_key] : null;
        if (!empty($filter_params)) {
            $baseUri = Cms::getIstance()->getApplicationUrl(null, true) . $post_type;

            $uriComponents = [];
            foreach ($filter_params as $k => $v) {
                if ($k !== $filter_key) {
                    $uriComponents[] = $k . '-' . $v;
                }
            }
            $baseUri .= !empty($uriComponents) ? implode('/', $uriComponents) . '/' : '';

            $filtro = \Filtri::findFirst([
                'conditions' => 'Filtri.key = ?1 AND Filtri.id_applicazione = ?2 AND id_filtro_parent IS NOT NULL',
                'bind'       => [1 => $filter_key, 2 => \apps\site\library\Cms::getIstance()->id_application],
                'cache'      => [
                    'key'      => 'dettaglio.filtroParent.' . $filter_key . \apps\site\library\Cms::getIstance()->id_application,
                    "lifetime" => 56400
                ]
            ]);

            if (!$filtro) return false;

            $key_fitro_parent = $filtro->FiltroParent->key;

            if (!$key_fitro_parent) return false;

            if (!array_key_exists($key_fitro_parent, $filter_params)) return false;

            $filter_value_selected = $filter_params[$key_fitro_parent];

            $fvCollegato = \FiltriValori::findFirst([
                'conditions' => 'key = :filter_value_selected: AND attivo =1',
                'bind'       => ['filter_value_selected' => $filter_value_selected],
                'cache'      => [
                    'key'      => 'filterRelatedValueFromkey' . $filter_value_selected . $filter_key,
                    'lifetime' => 56400
                ]
            ]);

            $valori = \FiltriValori::find([
                'columns'    => 'FiltriValori.id,
                            FiltriValori.valore AS titolo_valore, 
                            FiltriValori.key AS key_filtro_valore,
                            f.key AS key_filtro, 
                            FiltriValori.id_filtro_valore_parent',
                'conditions' => 'f.id = ?1 AND FiltriValori.attivo = 1 AND FiltriValori.id_filtro_valore_parent = ?2',
                'bind'       => [1 => $filtro->id, 2 => $fvCollegato->id],
                'joins'      => [
                    ['Filtri', 'f.id = FiltriValori.id_filtro AND f.attivo = 1', 'f', 'INNER'],
                    ['PostsFiltri', 'pf.id_filtro_valore = FiltriValori.id AND pf.attivo = 1', 'pf', 'INNER'],
                    ['Posts', 'p.id = pf.id_post AND p.id_tipologia_stato = 1 AND p.attivo = 1', 'p', 'INNER']
                ],
                'group'      => 'FiltriValori.id',
                'cache'      => [
                    'key'      => 'dettaglio.filtrivaloriParentJ.' . $filtro->id . \apps\site\library\Cms::getIstance()->id_application,
                    "lifetime" => 56400
                ]
            ])->toArray();

            if (empty($valori)) return false;

            $values = [];
            $i = 0;
            while (!empty($valori)) {
                if (!isset($valori[$i]) && $i > 0) $i = 0;

                $values[$valori[$i]['id']] = $valori[$i];

                if (!is_null($current_filter_value) && $current_filter_value == $valori[$i]['key_filtro_valore']) $values[$valori[$i]['id']]['active'] = true;

                unset($valori[$i]);

                $i++;
            }
            $active_widget_show_all = $current_filter_value !== null;

            $view = $this->getDi()->get('view');
            $rs = $view->getRender($post_type, $filter_key, ['filtro' => $filtro, 'filtri_valori' => $values, 'baseUri' => $baseUri, 'active_widget_show_all' => $active_widget_show_all], function ($view) {
                $view->setViewsDir("../apps/site/views/partials/menu/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
            return $rs;
        } else {
            return false;
        }

    }

    /**
     * @param $post_type_slug
     * @param $post
     * @param array $filters_conditions
     * @param int $limit
     * @return bool
     */
    public function renderRelatedWidget($post_type_slug, $post, $filters_conditions = [], $limit = 10)
    {
        /**
         * @var Db
         */
        $cache = $this->getDI()->get('viewCache');
        $cacheKey = $post_type_slug . ".RelatedPostForId." . $post->id;
        $rs = $cache->get($cacheKey);

        if (is_null($rs)) {

            $connection = $this->getDI()->getDb();
            $postTypeMetaFields = \EntityController::getPostTypeMetaFields($post_type_slug);
            $postTypeFilterFields = \EntityController::getPostTypeFilterFields($post_type_slug);
            $columns_select = [];
            $nr = count($postTypeMetaFields);
            for ($i = 0; $i < $nr; $i++) {
                $columns_select[] = "pm." . $postTypeMetaFields[$i] . " AS meta_" . $postTypeMetaFields[$i];
            }

            $n = count($postTypeFilterFields);
            for ($x = 0; $x < $n; $x++) {
                $columns_select[] = "pf." . $postTypeFilterFields[$x] . " AS filter_" . $postTypeFilterFields[$x];
            }

            $conditions = "";
            if (!empty($filters_conditions)) {
                $nr = count($filters_conditions);
                $conditionals = [];
                $conditions .= " AND (";
                for ($i = 0; $i < $nr; $i++) {
                    $cond = 'filter_' . $filters_conditions[$i];
                    $conditionals[] = " pf." . $filters_conditions[$i] . " = '" . $post->{$cond} . "' ";
                }
                $conditions .= implode(' OR ', $conditionals) . " ) ";

            }
            $id_applicazione = \apps\site\library\Cms::getIstance()->id_application;
            $query = "
                    SELECT
                      p.*,
                      f.*,
                      " . implode(',' . PHP_EOL, $columns_select) . ",
                      MATCH (p.titolo) AGAINST ('" . str_replace(['+', '*', '-', "'", '\/'], ' ', $post->titolo) . "' IN BOOLEAN MODE) AS relevance
                    FROM
                      _" . $post_type_slug . " p
                    INNER JOIN _" . $post_type_slug . "_meta pm ON pm.id_post = p.id_post
                    INNER JOIN _" . $post_type_slug . "_filter pf ON pf.id_post = p.id_post
                    INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1
                    WHERE
                        p.id_tipologia_stato = 1
                    AND
                        p.id_applicazione = '{$id_applicazione}'
                    AND
                        p.attivo = 1
                    AND
                        p.id_post != {$post->id_post}
                    AND
                        p.data_inizio_pubblicazione < NOW()
                    AND
                    (
                        p.data_fine_pubblicazione IS NULL 
                        OR
                        p.data_fine_pubblicazione > NOW()
                    )
                " . $conditions . "
                ORDER BY relevance DESC
                LIMIT 0, " . $limit;
            $q = $connection->query($query);
            $q->setFetchMode(\Phalcon\Db::FETCH_OBJ);
            $rs = $q->fetchAll();
            if ($rs) {
                $cache->save($cacheKey, $rs, 3600);
            } else {
                return false;
            }
        }
        if ($rs) {
            $view = $this->getDi()->get('view');
            $res = $view->getRender($post_type_slug, 'related', ['rs' => $rs], function ($view) {
                $view->setViewsDir("../apps/site/views/partials/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
            return $res;
        } else {
            return null;
        }
    }

    /**
     * @param $post_type_slug
     * @param $tpl
     * @param $limit
     * @return bool
     */
    public function renderWidgetLastEntities($post_type_slug, $tpl, $limit)
    {
        $cache = $this->getDI()->get('viewCache');
        $application = \apps\site\library\Cms::getIstance()->application;
        $cacheKey = $application . $post_type_slug . ".LastPosts." . $tpl . $limit;

        $rs = $cache->get($cacheKey);
        if (is_null($rs)) {
            $connection = $this->getDI()->getDb();
            $postTypeMetaFields = \EntityController::getPostTypeMetaFields($post_type_slug);
            $postTypeFilterFields = \EntityController::getPostTypeFilterFields($post_type_slug);
            $columns_select = [];
            $nr = count($postTypeMetaFields);
            for ($i = 0; $i < $nr; $i++) {
                $columns_select[] = "pm." . $postTypeMetaFields[$i] . " AS meta_" . $postTypeMetaFields[$i];
            }
            $n = count($postTypeFilterFields);
            for ($x = 0; $x < $n; $x++) {
                $columns_select[] = "pf." . $postTypeFilterFields[$x] . " AS filter_" . $postTypeFilterFields[$x];
            }

            $query = "
                    SELECT
                      p.*,
                      f.*,
                      " . implode(',' . PHP_EOL, $columns_select) . "
                    FROM
                      _" . $application . "_" . $post_type_slug . " p
                    INNER JOIN _" . $application . "_" . $post_type_slug . "_meta pm ON pm.id_post = p.id_post
                    INNER JOIN _" . $application . "_" . $post_type_slug . "_filter pf ON pf.id_post = p.id_post
                    INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1
                    WHERE
                        p.id_tipologia_stato = 1
                    AND
                        p.attivo = 1
                    AND
                        p.data_inizio_pubblicazione < NOW()
                    AND
                    (
                        p.data_fine_pubblicazione IS NULL 
                        OR
                        p.data_fine_pubblicazione > NOW()
                    )";
            if (!Cms::getIstance()->userLoggedIn) {
                $query .= " AND p.id_users_groups IS NULL ";
            } else {
                $auth = $this->getDi()->get('auth');
                $user = $auth->getIdentity();
                $query .= " AND (p.id_users_groups IS NULL OR FIND_IN_SET('" . $user['id_users_groups'] . "', p.id_users_groups) > 0) ";
            }
            $query .= "ORDER BY
                  p.data_inizio_pubblicazione DESC, p.id DESC 
                LIMIT 0, " . $limit;
            $q = $connection->query($query);
            $q->setFetchMode(\Phalcon\Db::FETCH_OBJ);
            $rs = $q->fetchAll();
            if ($rs) {

                $nr = count($rs);
                for ($i = 0; $i < $nr; $i++) {
                    $rs[$i]->readLink = $application == Cms::getIstance()->getConfig()->application->defaultCode ?
                        DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $rs[$i]->slug :
                        DIRECTORY_SEPARATOR . $application . DIRECTORY_SEPARATOR . $post_type_slug . DIRECTORY_SEPARATOR . $rs[$i]->slug;
                }

                $cache->save($cacheKey, $rs, 3600);
            } else {
                return false;
            }
        }

        $view = $this->getDi()->get('view');
        $rs = $view->getRender($post_type_slug, $tpl, ['rs' => $rs], function ($view) {
            $view->setViewsDir("../apps/site/views/partials/");
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
        return $rs;
    }

    /**
     * @param $post_object
     * @param $post_type_slug
     * @param int $limit
     * @return mixed
     */
    public function renderRelatedPostsWidget($post_object, $post_type_slug, $limit = 8)
    {
        $cache = $this->getDI()->get('viewCache');
        $application = \apps\site\library\Cms::getIstance()->application;
        $cacheKey = "RelatedPostsWidget" . $post_object->id . $application;
        $rs = $cache->get($cacheKey);

        if (is_null($rs)) {
            $connection = $this->getDI()->getDb();
            $news = $post_object->meta_news_related;
            $eventi = $post_object->meta_eventi_related;
            $prodotti = $post_object->meta_prodotti_related;
            $ricette = $post_object->meta_ricette_related;
            $luoghi = $post_object->meta_luoghi_related;

            /**
             * Se non ci sono entità collegate cerco l'id del post corrente tra i contenuti collegati degli altri post
             * e faccio backlining
             */
            if (is_null($news) && is_null($eventi) && is_null($prodotti) && is_null($ricette) && is_null($luoghi)) {

                $meta_key = $post_type_slug . '_related';

                $query = "
                    SELECT
                        GROUP_CONCAT(p.id) as ids,
                        tp.slug
                    FROM
                        posts p
                    INNER JOIN posts_meta pm ON pm.`post_id` = p.id AND pm.`id_tipologia_stato` = 1 AND pm.attivo = 1
                    INNER JOIN tipologie_post tp ON tp.id = p.id_tipologia_post AND tp.attivo = 1
                    WHERE
                        pm.`meta_key` = '{$meta_key}'
                    AND
                        FIND_IN_SET(" . $post_object->id_post . ", pm.`meta_value_varchar`)
                    GROUP BY tp.id
                    LIMIT 0," . $limit;
                $q = $connection->query($query);
                $q->setFetchMode(\Phalcon\Db::FETCH_OBJ);
                $rs = $q->fetchAll();
                if ($rs) {
                    foreach ($rs as $r) {
                        ${$r->slug} = $r->ids;
                    }
                }
                $nr_res = count($rs);

                /**
                 * Se ho meno di 4 risultati riempio i correlati con 4 contenuti con data
                 * inizio pubblicazione nello stesso periodo
                 */
                if ($nr_res < $limit) {
                    $limit = $limit - $nr_res;
                    $parameters = [
                        'columns'    => 'GROUP_CONCAT(Posts.id) as ids, tp.slug AS slug_tp',
                        'conditions' => '
                                Posts.id_tipologia_stato = 1 
                            AND 
                                tp.slug IN ("news", "eventi", "luoghi", "ricette", "prodotti")
                            AND 
                                DATE_FORMAT(Posts.data_inizio_pubblicazione,"%Y-%m-%d") < :data_inizio_pubblicazine_post_corrente: 
                            AND 
                                Posts.attivo = 1
                        ',
                        'bind'       => ['data_inizio_pubblicazine_post_corrente' => $post_object->data_inizio_pubblicazione],
                        'joins'      => [
                            [
                                'TipologiePost',
                                'tp.id = Posts.id_tipologia_post AND tp.attivo = 1',
                                'tp',
                                'INNER'
                            ]
                        ],
                        'limit'      => $limit,
                        'group'      => 'tp.id'
                    ];


                    $posts = \Posts::find($parameters);

                    foreach ($posts as $rp) {
                        if (isset(${$rp->slug_tp}) && !empty(${$rp->slug_tp})) {
                            ${$rp->slug_tp} .= ',' . $rp->ids;
                        } else {
                            ${$rp->slug_tp} = $rp->ids;
                        }
                    }
                }
            }

            $unions = [];

            /**
             * Recupero le informazioni basilari delle entità per comporre il widget
             */
            if (!empty($news)) {
                $unions[] = "SELECT p.id, p.id_post, p.titolo, p.excerpt, p.id_users_groups, p.data_inizio_pubblicazione, CONCAT('/news/', p.slug) AS slug, f.filename, f.alt, f.private FROM _" . $application . "_news p INNER JOIN _" . $application . "_news_meta pm ON pm.id_post = p.id_post INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1 WHERE p.id_post IN(" . $news . ") AND p.id_tipologia_stato = 1 AND p.attivo = 1 AND p.data_inizio_pubblicazione < NOW() AND ( p.data_fine_pubblicazione IS NULL OR p.data_fine_pubblicazione > NOW())";
            }
            if (!empty($eventi)) {
                $unions[] = "SELECT p.id, p.id_post, p.titolo, p.excerpt, p.id_users_groups, p.data_inizio_pubblicazione, CONCAT('/eventi/', p.slug) AS slug, f.filename, f.alt, f.private FROM _" . $application . "_eventi p INNER JOIN _" . $application . "_eventi_meta pm ON pm.id_post = p.id_post INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1 WHERE p.id_post IN(" . $eventi . ") AND p.id_tipologia_stato = 1 AND p.attivo = 1 AND p.data_inizio_pubblicazione < NOW() AND ( p.data_fine_pubblicazione IS NULL OR p.data_fine_pubblicazione > NOW())";
            }
            if (!empty($prodotti)) {
                $unions[] = "SELECT p.id, p.id_post, p.titolo, p.excerpt, p.id_users_groups, p.data_inizio_pubblicazione, CONCAT('/prodotti/', p.slug) AS slug, f.filename, f.alt, f.private FROM _" . $application . "_prodotti p INNER JOIN _" . $application . "_prodotti_meta pm ON pm.id_post = p.id_post INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1 WHERE p.id_post IN(" . $prodotti . ") AND p.id_tipologia_stato = 1 AND p.attivo = 1 AND p.data_inizio_pubblicazione < NOW() AND ( p.data_fine_pubblicazione IS NULL OR p.data_fine_pubblicazione > NOW())";
            }
            if (!empty($ricette)) {
                $unions[] = "SELECT p.id, p.id_post, p.titolo, p.excerpt, p.id_users_groups, p.data_inizio_pubblicazione, CONCAT('/ricette/', p.slug) AS slug, f.filename, f.alt, f.private FROM _" . $application . "_ricette p INNER JOIN _" . $application . "_ricette_meta pm ON pm.id_post = p.id_post INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1 WHERE p.id_post IN(" . $ricette . ") AND p.id_tipologia_stato = 1 AND p.attivo = 1 AND p.data_inizio_pubblicazione < NOW() AND ( p.data_fine_pubblicazione IS NULL OR p.data_fine_pubblicazione > NOW())";
            }
            if (!empty($luoghi)) {
                $unions[] = "SELECT p.id, p.id_post, p.titolo, p.excerpt, p.id_users_groups, p.data_inizio_pubblicazione, CONCAT('/luoghi/', p.slug) AS slug, f.filename, f.alt, f.private FROM _" . $application . "_luoghi p INNER JOIN _" . $application . "_luoghi_meta pm ON pm.id_post = p.id_post INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1 WHERE p.id_post IN(" . $luoghi . ") AND p.id_tipologia_stato = 1 AND p.attivo = 1 AND p.data_inizio_pubblicazione < NOW() AND ( p.data_fine_pubblicazione IS NULL OR p.data_fine_pubblicazione > NOW())";
            }

            // Unisco le subquery per fare un'unica richiesta al db
            $unions_string = implode(' UNION ', $unions);

            $query = "
                      SELECT
                        sel.*
                      FROM(
                            " . $unions_string . "
                      ) sel
                      WHERE
                      ";
            if (!Cms::getIstance()->userLoggedIn) {
                $query .= " sel.id_users_groups IS NULL ";
            } else {
                $auth = $this->getDi()->get('auth');
                $user = $auth->getIdentity();
                $query .= " sel.id_users_groups IS NULL OR FIND_IN_SET('" . $user['id_users_groups'] . "', sel.id_users_groups) > 0) ";
            }
            $query .= " ORDER BY sel.data_inizio_pubblicazione DESC, sel.id DESC LIMIT 0," . $limit;
            $q = $connection->query($query);
            $q->setFetchMode(\Phalcon\Db::FETCH_OBJ);
            $rs = $q->fetchAll();

            if ($rs) {
                $cache->save($cacheKey, $rs, 12000);
            } else {
                //salvo la cache anche se è vuota al fine di ripetere la lista delle query
                $cache->save($cacheKey, [], 12000);
            }
        }

        $view = $this->getDi()->get('view');
        $res = $view->getRender('partials', 'related', ['rs' => $rs], function ($view) {
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
        return $res;
    }

    /**
     * @param string $tpl
     * @return bool
     */
    public function renderNewsSlider($tpl = 'slides')
    {
        $cache = $this->getDI()->get('viewCache');
        $cacheNewsKey = "tag.lastNews";
        $lastNews = $cache->get($cacheNewsKey);
        /**
         * Ultime News
         */
        if (is_null($lastNews)) {
            $connection = $this->getDI()->getDb();
            $query = "
                SELECT rs.* FROM (
                    (SELECT
                        n.titolo,
                        n.excerpt,
                        DATE_FORMAT(n.data_inizio_pubblicazione, '%d/%m/%Y') AS data_inizio_pubblicazione,
                        CONCAT('/news/', n.slug) AS link,
                        CONCAT('/files/small/', f.filename) AS immagine,
                        f.alt AS alt_immagine
                    FROM 
                        _it_news n
                    INNER JOIN _news_meta nm ON nm.id_post = n.id_post 
                    INNER JOIN files f ON f.id = nm.immagine AND f.id_tipologia_stato = 1 AND f.attivo = 1
                    WHERE
                      n.id_applicazione = 1
                    AND 
                      n.attivo = 1
                    AND
                      n.id_tipologia_stato = 1
                    AND 
                      n.id_users_groups IS NULL
                    LIMIT 0,6)
                        
                    UNION
                                         
                    (SELECT
                        e.titolo,
                        e.excerpt,
                        DATE_FORMAT(e.data_inizio_pubblicazione, '%d/%m/%Y') AS data_inizio_pubblicazione,
                        CONCAT('/eventi/', e.slug) AS link,
                        CONCAT('/files/small/', f.filename) AS immagine,
                        f.alt AS alt_immagine
                    FROM 
                        _it_eventi e
                    INNER JOIN _eventi_meta em ON em.id_post = e.id_post 
                    INNER JOIN files f ON f.id = em.immagine AND f.id_tipologia_stato = 1 AND f.attivo = 1
                    WHERE
                      e.id_applicazione = 1
                    AND 
                      e.attivo = 1
                    AND
                      e.id_tipologia_stato = 1
                    AND 
                      e.id_users_groups IS NULL
                    LIMIT 0,6)
                ) rs
                ORDER BY rs.data_inizio_pubblicazione DESC 
                LIMIT 0,6";
            $lastNews = $connection->query($query)->fetchAll();

            if ($lastNews && !empty($lastNews)) {
                $cache->save($cacheNewsKey, $lastNews, 3600);
            }
        }
        if (is_null($lastNews) || !$lastNews || empty($lastNews)) return false;

        $view = $this->getDi()->get('view');
        $rs = $view->getRender('lastNews', $tpl, ['lastNews' => $lastNews], function ($view) {
            $view->setViewsDir("../apps/site/views/partials/");
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
        return $rs;
    }

    /**
     * @param string $string
     * @param int $your_desired_width
     * @return string
     */
    public function wordwrapString($string = "", $your_desired_width = 15)
    {
        $return = substr($string, 0, strpos(wordwrap($string, $your_desired_width), "\n"));
        return $return;
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDateEventList($date)
    {
        $mesi = [
            '01' => 'Gen',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mag',
            '06' => 'Giu',
            '07' => 'Lug',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Ott',
            '11' => 'Nov',
            '12' => 'Dic'
        ];
        $strt = strtotime($date);
        return date('d', $strt) . ' <span>' . $mesi[date('m', $strt)] . '</span>';
    }

    /**
     * @param null $application
     * @param bool $relative
     * @return string
     */
    public function getApplicationUrl($application = null, $relative = false)
    {
        return Cms::getIstance()->getApplicationUrl($application = null, $relative = false);
    }
}