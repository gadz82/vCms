<?php
namespace apps\api\library;
class Cms {

    public static $instance = null;
    /**
     * @var self
     */
    public $id_application;
    public $application;
    public $adminLoggedIn;
    public $userLoggedIn;

    private function __construct(){}

    public static function getIstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

}