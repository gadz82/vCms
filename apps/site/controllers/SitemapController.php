<?php

class SitemapController extends ControllerBase
{

    protected $domain;


    public function initialize()
    {
        //for big files, to be on the safe side
        set_time_limit(0);
        $this->domain = $this->config->application->protocol . $this->config->application->siteUri;
        parent::initialize();
        if ($this->application !== $this->config->application->defaultCode) $this->domain .= DIRECTORY_SEPARATOR . $this->application;

    }

    public function indexAction()
    {
        $response = new Phalcon\Http\Response();

        $expireDate = new \DateTime();
        $expireDate->modify('+1 day');

        $response->setExpires($expireDate);

        $response->setHeader('Content-Type', "application/xml; charset=UTF-8");
        $response->setHeader('X-Robots-Tag', "noindex");

        $sitemap = new \DOMDocument("1.0", "UTF-8");

        $urlset = $sitemap->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        $tipologie_post = TipologiePost::find();
        $links = [];

        foreach ($tipologie_post as $post_type) {
            $rs = $this->getEntities($post_type->slug);

            $entity_base_url = '/' . $post_type->slug;

            $postTypeFilterFields = self::getPostTypeFilterFields($post_type->slug);
            $n = count($postTypeFilterFields);
            if ($post_type->slug !== 'pagina') $links[] = $entity_base_url . '/';
            foreach ($rs as $e) {
                if ($post_type->slug !== 'pagina' && $e->slug !== 'index') {
                    $links[] = $entity_base_url . '/' . $e->slug;
                }
                $sub_models = [];

                for ($i = 0; $i < $n; $i++) {
                    if ($i % 2) continue;

                    $f = $postTypeFilterFields[$i];
                    if (is_null($e->{'filter_key_' . $f})) {
                        continue;
                    }
                    $filter_string = $f . '-' . $e->{'filter_key_' . $f};
                    $url_listing_filtered = '/' . $post_type->slug . '/list/' . $filter_string;

                    if (!in_array($url_listing_filtered, $links)) {
                        $links[] = $url_listing_filtered;
                    }

                    if (!in_array($filter_string, $sub_models)) {
                        $sub_models[] = $filter_string;
                    }
                }

                $nr = count($sub_models);
                $c = 0;
                for ($i = 1; $c < $nr; $i++) {
                    $base = $sub_models[$c];
                    if (isset($sub_models[$i])) {
                        $base .= '/' . $sub_models[$i];
                        $links[] = '/' . $post_type->slug . '/list/' . $base;
                    } else {
                        $c++;
                        $i = ($c + 1);
                    }
                }
            }
        }

        $tags = Tags::query()
            ->columns('Tags.tag, tp.slug AS tipologia_post')
            ->innerJoin('PostsTags', 'pt.id_tag = Tags.id AND pt.attivo = 1', 'pt')
            ->innerJoin('Posts', 'p.id = pt.id_post AND p.id_tipologia_stato = 1 AND p.attivo = 1', 'p')
            ->innerJoin('TipologiePost', 'tp.id = p.id_tipologia_post AND tp.attivo = 1', 'tp')
            ->groupBy('Tags.id')
            ->execute();

        if ($tags) {
            $nr = count($tags);
            for ($i = 0; $i < $nr; $i++) {
                $links[] = '/' . $tags[$i]->tipologia_post . '/tag/' . $tags[$i]->tag;
            }
        }

        $modifiedAt = new \DateTime();
        $modifiedAt->setTimezone(new \DateTimeZone('UTC'));
        $comment = $sitemap->createComment(' Last update of sitemap ' . date("Y-m-d H:i:s") . ' ');
        $urlset->appendChild($comment);

        foreach ($links as $link) {
            $url = $sitemap->createElement('url');
            $href = $this->domain . $link;
            $url->appendChild($sitemap->createElement('loc', $href));
            $url->appendChild($sitemap->createElement('changefreq', 'daily')); //Hourly, daily, weekly etc.
            $url->appendChild($sitemap->createElement('priority', '0.7'));     //1, 0.7, 0.5 ...
            $urlset->appendChild($url);
        }

        $url = $sitemap->createElement('url');
        $href = $this->application == $this->config->application->defaultCode ? $this->domain : $this->domain . DIRECTORY_SEPARATOR;
        $url->appendChild($sitemap->createElement('loc', $href));
        $url->appendChild($sitemap->createElement('changefreq', 'daily')); //Hourly, daily, weekly etc.
        $url->appendChild($sitemap->createElement('priority', '1.0'));     //1, 0.7, 0.5 ...
        $urlset->appendChild($url);
        $sitemap->appendChild($urlset);
        $response->setHeader('Content-Type', 'application/xml');
        $response->setContent($sitemap->saveXML());
        return $response;
    }

    private function getEntities($post_type_slug)
    {

        $cache = $this->getDI()->get('viewCache');
        $cacheKey = "sitemap.entities." . $post_type_slug;
        $entities = $cache->get($cacheKey);
        $application = $this->application;
        if (is_null($entities)) {
            $postTypeMetaFields = self::getPostTypeMetaFields($post_type_slug);
            $postTypeFilterFields = self::getPostTypeFilterFields($post_type_slug);
            $columns_select = [];
            $nr = count($postTypeMetaFields);
            for ($i = 0; $i < $nr; $i++) {
                $columns_select[] = "em." . $postTypeMetaFields[$i] . " AS meta_" . $postTypeMetaFields[$i];
            }

            $n = count($postTypeFilterFields);
            for ($x = 0; $x < $n; $x++) {
                $columns_select[] = "ef." . $postTypeFilterFields[$x] . " AS filter_" . $postTypeFilterFields[$x];
            }
            $query = "
                SELECT
                  e.*,
                  " . implode(',' . PHP_EOL, $columns_select) . "
                FROM
                  `_" . $application . "_" . $post_type_slug . "` e
                INNER JOIN  _" . $application . "_" . $post_type_slug . "_meta em ON em.id_post = e.id_post
                INNER JOIN _" . $application . "_" . $post_type_slug . "_filter ef ON ef.id_post = e.id_post
                WHERE
                    e.id_tipologia_stato = 1
                AND
                    e.data_inizio_pubblicazione < NOW()
                AND
                    e.attivo = 1
                ORDER BY e.data_inizio_pubblicazione DESC, e.id DESC
            ";

            $q = $this->connection->query($query);
            $q->setFetchMode(Phalcon\Db::FETCH_OBJ);
            $entities = $q->fetchAll();

            if ($entities && !empty($entities)) {
                $cache->save($cacheKey, $entities, 3600);
            }
        }
        return $entities;
    }
}