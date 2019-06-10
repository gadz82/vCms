<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 26/03/2019
 * Time: 11:17
 */
namespace apps\site\library\helpers;
use Phalcon\Mvc\User\Component;
use Phalcon\Tag;

class GoogleStaticMapHelper extends Component{

    private static $staticMapUrl = 'https://maps.googleapis.com/maps/api/staticmap?';

    private static $mapApiUrl = 'https://www.google.com/maps/search/?api=1&query=';
    public $moreMarkers;
    protected $mapParams = [
        'zoom' => 9,
        'size' => '480x320',
        'map_type' => 'roadmap',
        'markers' => '',
        'sensor' => false,
        'key' => ''
    ];

    public function setCenter($lat, $lng){
    $this->mapParams['center'] = $lat.','.$lng;
    }

    public function setZoom($zoomInt){
        $this->mapParams['zoom'] = $zoomInt;
    }

    public function setMarker($lat, $lng){
        $this->mapParams['markers'] = $lat.",".$lng;
    }

    public function addMarker($lat, $lng){
        $this->moreMarkers.= "&makers=".$lat.",".$lng;
    }

    public function setSize($width, $height){
        $this->mapParams['size'] = $width.'x,'.$height;
    }

    public function setMapType($map_type){
        $this->mapParams['map_type'] = $map_type;
    }

    public function getStaticMapUrl(){
        $this->mapParams['key'] = $this->config->google->apiKey;
        $imgSrc = self::$staticMapUrl.http_build_query($this->mapParams);

        if(!empty($this->moreMarkers)){
            $imgSrc.= $this->moreMarkers;
        }

        return Tag::tagHtml(
            'img',
            [
                'src' => $imgSrc
            ]
        );
    }

    public function getMapUrl($lat, $lng){
        return self::$mapApiUrl
            .$lat
            .','
            .$lng;
    }
}