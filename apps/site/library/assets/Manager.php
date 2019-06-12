<?php

namespace apps\site\library\assets;

/**
 * Manages collections of CSS/Javascript assets
 */
class Manager extends \Phalcon\Assets\Manager
{
    protected $css_expiration_token;
    protected $js_expiration_token;
    protected $assets_collections_expiration_token;

    /**
     * Phalcon\Assets\Manager
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        $this->css_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_css"',
            'cache'      => [
                'key'      => 'version_number_css',
                'lifetime' => 432000
            ]
        ])->option_value;

        $this->js_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_js"',
            'cache'      => [
                'key'      => 'version_number_css',
                'lifetime' => 432000
            ]
        ])->option_value;

        $this->assets_collections_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_assets_collections"',
            'cache'      => [
                'key'      => 'version_number_css',
                'lifetime' => 432000
            ]
        ])->option_value;
        parent::__construct();
    }

    /**
     * Adds a Css resource to the 'css' collection
     *
     * <code>
     * $assets->addCss("css/bootstrap.css");
     * $assets->addCss("http://bootstrap.my-cdn.com/style.css", false);
     * </code>
     *
     * @param string $path
     * @param mixed $local
     * @param mixed $filter
     * @param mixed $attributes
     * @return Manager
     */
    public function addCss($path, $local = true, $filter = true, $attributes = null)
    {
        if ($local && !empty($this->css_expiration_token)) {
            $path .= '?v=' . $this->css_expiration_token;
        }
        parent::addCss($path, $local = true, $filter = true, $attributes = null);
    }


    /**
     * Adds a javascript resource to the 'js' collection
     *
     * <code>
     * $assets->addJs("scripts/jquery.js");
     * $assets->addJs("http://jquery.my-cdn.com/jquery.js", false);
     * </code>
     *
     * @param string $path
     * @param mixed $local
     * @param mixed $filter
     * @param mixed $attributes
     * @return Manager
     */
    public function addJs($path, $local = true, $filter = true, $attributes = null)
    {
        if ($local && !empty($this->js_expiration_token)) {
            $path .= '?v=' . $this->js_expiration_token;
        }
        parent::addJs($path, $local = true, $filter = true, $attributes = null);
    }

    public function collection($name)
    {
        if (is_null($this->_collections)) $this->_collections = [];
        if (!array_key_exists($name, $this->_collections)) {
            $collection = new Collection($this->assets_collections_expiration_token);
            $this->_collections[$name] = $collection;
        }
        return $this->_collections[$name];
    }
}
