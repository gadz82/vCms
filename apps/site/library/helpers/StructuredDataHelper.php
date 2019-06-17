<?php
namespace apps\site\library\helpers;
use apps\site\library\Cms;
use Phalcon\Di;

/**
 * Class StructuredDataHelper
 * @package apps\site\library\helpers
 */
class StructuredDataHelper
{

    /**
     * @var StructuredDataHelper
     */
    private static $istance = null;

    /**
     * @var array
     */
    protected $structs = [];

    /**
     * @return StructuredDataHelper
     */
    public static function getIstance()
    {
        if (is_null(self::$istance)) {
            self::$istance = new self;
        }
        return self::$istance;
    }

    /**
     * StructuredDataHelper constructor.
     */
    private function __construct()
    {
        $this->addDefault();
    }

    /**
     * Add default structs
     */
    protected function addDefault()
    {
        $config = Cms::getIstance()->getConfig();
        $this->addStruct(
            'Website',
            [
                "@context"      => "https://schema.org",
                "@type"         => "WebSite",
                "name"          => $config->application->appName,
                "alternateName" => $config->application->appDescription,
                "url"           => Cms::getIstance()->getApplicationUrl()
            ]
        );
    }

    /**
     * @param $type
     * @param $struct
     */
    public function addStruct($type, $struct)
    {
        $this->structs[$type] = $struct;
    }

    /**
     * @param $type
     * @return mixed|null
     */
    public function getStructByType($type)
    {
        return array_key_exists($type, $this->structs) ? $this->structs[$type] : null;
    }

    /**
     * @return string
     */
    public function getStructs()
    {
        return json_encode(array_values($this->structs));
    }

    /**
     * @return array
     */
    public function getStructsObject(){
        return $this->structs;
    }

}