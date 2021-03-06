<?php
namespace apps\site\library;

use Phalcon\Di;

class Cms
{
    /**
     * @var self
     */
    public static $instance = null;

    /**
     * @var integer
     */
    public $id_application;

    /**
     * @var string
     */
    public $applicationHrefLang;

    /**
     * @var Application Code
     */
    public $application;

    /**
     * @var bool
     */
    public $adminLoggedIn;

    /**
     * @var bool
     */
    public $userLoggedIn;

    /**
     * @var \Phalcon\Config
     */
    protected $config;

    /**
     * Cms constructor.
     */
    private function __construct()
    {
        $this->config = Di::getDefault()->get('config');
    }

    /**
     * @return Cms|null
     */
    public static function getIstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param null $application
     * @return string
     */
    public function getApplicationUrl($application = null, $relative = false)
    {
        $r = (!$relative ? $this->getBaseUrl() : '')
            . $this->config->application->baseUri;

        return $this->config->application->multisite ?
            ($application !== null ? $application : $this->application) == $this->config->application->defaultCode ? $r : $r . $this->application . DIRECTORY_SEPARATOR :
            $r;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->config->application->protocol
        . $this->config->application->siteUri;
    }

    /**
     * @return \Phalcon\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

}