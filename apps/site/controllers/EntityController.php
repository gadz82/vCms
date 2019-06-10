<?php

/**
 * Class EntityController
 */
class EntityController extends ControllerBase
{
    protected $post;

    /**
     * Bootstrap Entity Controller
     */
    public function initialize(){
        $this->tag->setTitle('Entity');
        parent::initialize();
    }

    /**
     * Action delegated to render the single entity information
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function readAction(){
        $post_type = $this->dispatcher->getParam('post_type_slug');
        $slug = $this->dispatcher->getParam('post_slug');
        if(!$this->connection->tableExists('_'.$this->application.'_'.$post_type)){
            return $this->response->redirect('404');
        }

        $post = $this->getPostBySlug($slug, $post_type);
        if(!$post) return $this->response->redirect('404');
        if(!$post->authorized){
            $this->response->setHeader('X-Robots-Tag', 'noindex');
            $this->flashSession->error('Accedi o registrati per visualizzare il contenuto');
            return $this->dispatcher->forward([
                'controller' => 'users',
                'action'     => 'index'
            ]);
        }

        $this->tags->setTitle($post->meta_meta_title);
        $this->tags->setOgTitle($post->meta_og_title);
        $this->tags->setOgDescription($post->meta_og_description);
        $this->tags->setOgUrl($this->config->application->protocol.$this->config->application->siteUri.'/'.$post_type.'/'.$slug);

        $canonical = \apps\site\library\Cms::getIstance()->getApplicationUrl();
        if($post_type !== 'pagina'){
            $canonical.= $post_type;
        }
        $canonical.= '/'.$slug;

        $this->tags->setCanonicalUrl($canonical);
        $this->tags->setRobots($post->meta_robots);

        if(!empty($post->meta_video_url)){
            $this->tags->setOgVideo($post->meta_video_url);
        }

        if(!empty($post->meta_og_image)){
            $og_image = Files::findFirst([
                'conditions' => 'id = ?1',
                'bind' => [
                    1 => $post->meta_og_image
                ],
                'cache' => [
                    "key" => "ogMetaImage".$post->meta_og_image,
                    "lifetime" => 12000
                ]
            ]);
            if($og_image) $this->tags->setOgImage($og_image->fileurl);
        }

        $this->tags->setMetaDescription($post->meta_meta_description);

        /**
         * View personalizzate in base allo slug
         */
        if($this->view->exists($post_type.'/'.$slug)){
            $this->view->pick($post_type.'/'.$slug);
        } else {
            if(!$this->view->exists($post_type.'/read')) $this->response->redirect('404');
            $this->view->pick($post_type.'/read');
        }
        $this->view->post = $this->post = $post;

        $this->view->post_type = TipologiePost::findFirst([
            'conditions' => 'slug = ?1 AND attivo = 1',
            'bind' => [1 => $post_type],
            'cache' => [
                "key" => "getPostType".$post_type."Detail",
                "lifetime" => 12000
            ]
        ]);

        $assets = ['owlCarousel'];
        $this->addLibraryAssets($assets, 'entity-read-'.$post_type);

    }

    /**
     * Recupera info dal db
     *
     * @param $slug
     * @param $post_type_slug
     * @return bool | object
     */
    public function getPostBySlug($slug, $post_type_slug){
        $cache = $this->getDI()->get('viewCache');
        $cacheKey = $post_type_slug.".".$slug;
        $rs = $cache->get($cacheKey);

        if (is_null($rs)) {
            $postTypeMetaFields = self::getPostTypeMetaFields($post_type_slug);
            $postTypeFilterFields = self::getPostTypeFilterFields($post_type_slug);

            $columns_select = [];
            $nr = count($postTypeMetaFields);
            for($i = 0; $i < $nr; $i++){
                $columns_select[] = "pm.".$postTypeMetaFields[$i]." AS meta_".$postTypeMetaFields[$i];
            }

            $n = count($postTypeFilterFields);
            for($x = 0; $x < $n; $x++){
                $columns_select[] = "pf.".$postTypeFilterFields[$x]." AS filter_".$postTypeFilterFields[$x];
            }
            $tableName = '_'.$this->application.'_'.$post_type_slug;
            $columns = !empty($columns_select) ? implode(','.PHP_EOL, $columns_select)."," : '';
            $query = "
                SELECT
                  p.*,
                  ".$columns;
            if(!$this->isUserLoggedIn){
                $query.= "IF(p.id_users_groups IS NULL, 1, 0) AS authorized ";
            } else {
                $query.= "IF(p.id_users_groups IS NOT NULL, IF(FIND_IN_SET('".$this->user['id_users_groups']."', p.id_users_groups) > 0, 1, 0), 1) AS authorized ";
            }
            $query.="FROM
                 `".$tableName."` p
                INNER JOIN `".$tableName."_meta` pm ON pm.id_post = p.id_post
                INNER JOIN `".$tableName."_filter` pf ON pf.id_post = p.id_post
                WHERE
                    p.slug = '{$slug}'
                AND
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
                )
            ";
            //
            $q = $this->connection->query($query);
            $q->setFetchMode(Phalcon\Db::FETCH_OBJ);
            $rs = $q->fetch();
            if(!$rs) return false;

            if(isset($rs->meta_immagine) && !empty($rs->meta_immagine)){
                $rs->immagine = Files::findFirst([
                    'conditions' => 'id = ?1',
                    'bind' => [1 => $rs->meta_immagine],
                    'cache' => [
                        "key" => $cacheKey.'.immagine',
                        "lifetime" => 3600
                    ]
                ]);
            }

            if(isset($rs->meta_immagini_gallery) && !empty($rs->meta_immagini_gallery)){
                $rs->meta_immagini_gallery = Files::find([
                    'conditions' => 'id IN('.$rs->meta_immagini_gallery.') AND attivo = 1',
                    'order' => 'priorita',
                    'cache' => [
                        "key" => $cacheKey.'.immagini_gallery',
                        "lifetime" => 3600
                    ]
                ]);
            }
            $cache->save($cacheKey, $rs, 3600);
            return $rs;
        } else {
            return $rs;
        }

    }


}