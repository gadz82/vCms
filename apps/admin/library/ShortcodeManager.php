<?php
namespace apps\admin\library;

use apps\admin\plugins\FlatTablesManagerPlugin;
use Phalcon\Tag;

/**
 * Class ShortcodeManager
 * Applica gli shortcode registrati prima che i contenuti atomizzati vengano normalizzati e inseriti nelle flat tables
 * Il metodo shortcodify viene invocato nel FlatTableManagerPlugin
 *
 * @see FlatTablesManagerPlugin
 *
 * @package apps\admin\library
 */
class ShortcodeManager extends Tag{

    /**
     * @var \Phalcon\Cache\Frontend\Output
     */
    protected $cache;

    /**
     * @var \Posts
     */
    protected $post = null;

    public function __construct()
    {
        $this->cache = $this->getDI()->get('viewCache');
    }

    public function shortcodify($string, $id_post){
        return preg_replace_callback('#\[\[(.*?)\]\]#', function ($matches) {
            if(is_null($this->post) || $this->post->id !== $id_post) $this->post = \Posts::findFirst($id_post);
            $whitespace_explode = explode("||", $matches[1]);
            $fnName = 'shortcode_'.array_shift($whitespace_explode);
            $args = explode(",", $whitespace_explode[0]);
            return method_exists($this, $fnName) ?
                call_user_func_array([$this, $fnName],$args) :
                $matches[0];
        }, $string);
    }

}