<?php

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 24/04/17
 * Time: 12:48
 */
class ApiController extends \Phalcon\Mvc\Controller
{
    /**
     * @var \Phalcon\Db\Adapter\Pdo\Mysql
     */
    protected $connection;

    /**
     * Current Application Object
     * @var array
     */
    protected $application;

    /**
     * Current application id
     * @var
     */
    protected $id_application;

    public static function getPostTypeMetaFields($post_type_slug)
    {
        $app = \apps\api\library\Cms::getIstance()->application;
        $postTypeMetaFields = Options::findFirst([
            'conditions' => 'option_name = "columns_map_' . $app . '_' . $post_type_slug . '_meta" AND attivo = 1',
            'cache'      => [
                "key"      => $app . '_' . $post_type_slug . ".meta_fields",
                "lifetime" => 56400
            ]
        ]);
        if (!$postTypeMetaFields) return false;
        return json_decode($postTypeMetaFields->option_value, true);
    }

    public static function getPostTypeFilterFields($post_type_slug)
    {
        $app = \apps\api\library\Cms::getIstance()->application;
        $postTypeFilterFields = Options::findFirst([
            'conditions' => 'option_name = "columns_map_' . $app . '_' . $post_type_slug . '_filter" AND attivo = 1',
            'cache'      => [
                "key"      => $app . '_' . $post_type_slug . ".filter_fields",
                "lifetime" => 56400
            ]
        ]);
        if (!$postTypeFilterFields) return false;
        return json_decode($postTypeFilterFields->option_value, true);
    }

    public static function slugify($text, $minus = false)
    {
        $r = $minus ? '-' : '_';
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', $r, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $r);

        // remove duplicate -
        $text = preg_replace('~-+~', $r, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    protected static function isTimestamp($string)
    {
        try {
            new DateTime('@' . $string);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    protected static function getCurrentAppVersion()
    {
        $av = Options::findFirst([
            'conditions' => 'option_name = "current_app_version" AND attivo = 1',
            'cache'      => [
                "key"      => "current_app_version",
                "lifetime" => 12000
            ]
        ]);
        if (!$av) return '1';
        return $av->option_value;
    }

    public function initialize()
    {
        $this->connection = $this->getDI()->getDb();
        $this->application = \apps\api\library\Cms::getIstance()->application;
        $this->id_application = \apps\api\library\Cms::getIstance()->id_application;
    }

    public function indexAction()
    {

    }

    public function setResponse($data)
    {

        $this->response->setJsonContent($data);
        /**
         * JsonP
         */
        $cb = $this->request->get('callback');

        if ($cb && !empty($cb)) {
            $this->response->setContent($cb . '(' . $this->response->getContent() . ')');
        }
        return $this->response;
    }

    protected function beginTransaction()
    {
        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        return $manager->get();
    }

}