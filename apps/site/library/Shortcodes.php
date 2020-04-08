<?php
namespace apps\site\library;

use apps\site\library\helpers\VideoHelper;
use Phalcon\Di;
use Phalcon\Mvc\View;
use Phalcon\Tag;

class Shortcodes extends Tag
{

    /**
     * @var \Phalcon\Cache\Frontend\Output
     */
    protected $cache;

    public function __construct()
    {
        $this->cache = $this->getDI()->get('viewCache');
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, 'shortcode_' . $name)) {
            call_user_func_array([$this, $name], $arguments);
        }
    }

    public function shortcodify($string)
    {
        return preg_replace_callback('#\[\[(.*?)\]\]#', function ($matches) {
            $whitespace_explode = explode("||", $matches[1]);
            $fnName = 'shortcode_' . array_shift($whitespace_explode);
            $args = explode(",", $whitespace_explode[0]);
            return method_exists($this, $fnName) ?
                $this->executeShortcode($fnName, $args) :
                '';
        }, $string);
    }

    public function shortcode_renderButton($link = "#", $button_text = "", $button_class = "button", $container_class = "col text-center")
    {

        return parent::tagHtml('div', ['class' => $container_class]) .
        parent::tagHtml('a', ['class' => 'button ' . $button_class, 'href' => $link]) .
        $button_text .
        parent::tagHtmlClose('a') .
        parent::tagHtmlClose('div');

    }

    private function executeShortcode($fnName, $args = [])
    {
        $cacheKey = $fnName . implode('|', $args);
        $rs = $this->cache->get($cacheKey);
        if (is_null($rs)) {
            $rs = call_user_func_array([$this, $fnName], $args);
            $this->cache->save($cacheKey, $rs, 14400);
            return $rs;
        } else {
            return $rs;
        }
    }

    public function shortcode_renderBlock($block_key, $html_tag = null, $html_tag_class = null)
    {
        $output = Tags::renderBlock($block_key);
        if (!is_null($html_tag)) {
            $output = is_null($html_tag_class) ?
                parent::tagHtml($html_tag) . $output . parent::tagHtmlClose($html_tag) :
                parent::tagHtml($html_tag, ['id' => 'id_' . $html_tag_class, 'class' => $html_tag_class]) . $output . parent::tagHtmlClose($html_tag);

        }
        return $output;
    }

    public function shortcode_renderEntry($post_type, $id, $class = 'entry clearfix col-xs-12')
    {
        $cacheKey = "ShortcodeRenderEntry." . $post_type . "." . $id;
        $rs = $this->cache->get($cacheKey);

        if (is_null($rs)) {
            $connection = $this->getDI()->getDb();
            $app = \apps\site\library\Cms::getIstance()->application;
            $table_name = "_" . $app . "_" . $post_type;
            $query = "
                    SELECT
                      p.titolo,
                      p.slug,
                      p.excerpt,
                      p.data_inizio_pubblicazione,
                      f.filename,
                      f.alt
                    FROM
                      " . $table_name . " p
                    INNER JOIN " . $table_name . "_meta pm ON pm.id_post = p.id_post
                    INNER JOIN files f ON f.id = pm.immagine AND f.attivo = 1
                    WHERE
                        p.id_post = '{$id}'
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
                    LIMIT 0,1
                ";
            $q = $connection->query($query);
            $q->setFetchMode(\Phalcon\Db::FETCH_OBJ);
            $rs = $q->fetch();
            if ($rs) {
                $this->cache->save($cacheKey, $rs, 7200);
            } else {
                return false;
            }
        }
        if ($rs) {
            $view = $this->getDi()->get('view');
            if (!$view->exists('partials/' . $post_type . '/oneEntry')) return null;
            $res = $view->getRender('news', 'oneEntry', ['entry' => $rs, 'class' => $class], function ($view) use ($post_type) {
                $view->setViewsDir("../apps/site/views/partials/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
            return $res;
        } else {
            return null;
        }

    }

    public function shortcode_renderForm($form_key, $html_tag = null, $html_tag_class = null)
    {
        $tags = Di::getDefault()->get('tags');
        $output = $tags->renderForm($form_key);
        if (!is_null($html_tag)) {
            $output = is_null($html_tag_class) ?
                parent::tagHtml($html_tag) . $output . parent::tagHtmlClose($html_tag) :
                parent::tagHtml($html_tag, ['id' => 'id_' . $html_tag_class, 'class' => $html_tag_class]) . $output . parent::tagHtmlClose($html_tag);

        }
        return $output;
    }

    public function shortcode_embedVideo($url, $autoplay = false)
    {
        $videoHelper = new VideoHelper($url, $autoplay);
        $embed = $videoHelper->render_embed();
        return parent::tagHtml('div', ['class' => 'rwd-video']) .
        $embed .
        parent::tagHtmlClose('div');
    }

    public function shortcode_renderImage($fileId, $size = null, $alt = null, $class = null)
    {

        $file = Files::findFirst([
            'conditions' => 'id = ?1 AND filetype LIKE "image%"',
            'bind'       => [1 => $fileId],
            'cache'      => [
                'key'      => 'ShortcodeRenderImage' . $fileId,
                'lifetime' => 32800
            ]
        ]);

        if (!$file) return;

        $baseUri = Cms::getIstance()->getConfig()->application->baseUri;
        $url = $file->private ?
            (is_null($size) ? $baseUri . 'media/render/' . $file->filename : $baseUri . 'media/render/' . $file->filename . '?size=' . $size) :
            (is_null($size) ? $baseUri . 'files/' . $file->filename : $baseUri . 'files/' . $size . '/' . $file->filename);

        $altText = !is_null($alt) ? $alt : $file->alt;

        return parent::tagHtml('img', ['class' => $class, 'src' => $url, 'alt' => $altText], true);

    }

    public function shortcode_downloadButton($id_file, $button_text = "Scarica", $button_class = "button", $container_class = "col-xs-12 text-center")
    {
        $file = \Files::findFirst([
            'conditions' => 'id = :id:',
            'bind'       => ['id' => $id_file],
            'cache'      => [
                "key"      => "ShortcodeDownloadButton" . $id_file,
                "lifetime" => 32800
            ]
        ]);
        if (!$file) return false;

        $file_href = $file->private == '1' ? '/media/render/' . $file->filename : '/files/' . $file->filename;

        return parent::tagHtml('div', ['class' => $container_class]) .
        parent::tagHtml('a', ['class' => 'button ' . $button_class, 'href' => $file_href, 'download' => 1]) .
        $button_text .
        parent::tagHtmlClose('a') .
        parent::tagHtmlClose('div');

    }

    public function shortcode_translate($string)
    {
        $translation = \FlatTranslations::findFirst([
            'columns'    => 'translation',
            'conditions' => 'original_string = ?1 AND id_applicazione = ?2 AND attivo = 1',
            'bind'       => [1 => $string, 2 => \apps\site\library\Cms::getIstance()->id_application],
            'cache'      => [
                'key'      => 'translationFor' . $string . \apps\site\library\Cms::getIstance()->id_application,
                'lifetime' => 360000
            ]
        ]);
        return $translation ? $translation->translation : $string;
    }


}