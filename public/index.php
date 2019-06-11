<?php
include "../vendor/autoload.php";

use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;

if (function_exists('apcu_add') && !function_exists('apc_add')) {
    function apc_add() {
        return call_user_func_array('apcu_add', func_get_args());
    }

    function apc_cache_info() {
        return call_user_func_array('apcu_cache_info', func_get_args());
    }

    function apc_cas() {
        return call_user_func_array('apcu_cas', func_get_args());
    }

    function apc_clear_cache() {
        return call_user_func_array('apcu_clear_cache', func_get_args());
    }

    function apc_dec() {
        return call_user_func_array('apcu_dec', func_get_args());
    }

    function apc_delete() {
        return call_user_func_array('apcu_delete', func_get_args());
    }

    function apc_exists() {
        return call_user_func_array('apcu_exists', func_get_args());
    }

    function apc_fetch() {
        return call_user_func_array('apcu_fetch', func_get_args());
    }

    function apc_inc() {
        return call_user_func_array('apcu_inc', func_get_args());
    }

    function apc_sma_info() {
        return call_user_func_array('apcu_sma_info', func_get_args());
    }

    function apc_store() {
        return call_user_func_array('apcu_store', func_get_args());
    }

    class APCIterator extends APCUIterator {}
}

class Application extends BaseApplication
{

    public function main()
    {
        if(!file_exists('.cmsinstalled')) header('Location: /install.php');
        $this->registerServices();
        define('FILES_DIR', __DIR__.'/files/');
        define('ABSOLUTE_DIR', str_replace('public', '', __DIR__));

        // Register the installed modules
        $this->registerModules([
            'site' => [
                'className' => 'CMSIO\Site\Module',
                'path'      => '../apps/site/Module.php',
            ],
            'admin'  => [
                'className' => 'CMSIO\Admin\Module',
                'path'      => '../apps/admin/Module.php',
            ],
            'api'  => [
                'className' => 'CMSIO\Api\Module',
                'path'      => '../apps/api/Module.php',
            ],
            'cron'  => [
                'className' => 'CMSIO\Cron\Module',
                'path'      => '../apps/cron/Module.php',
            ]
        ]);

        if($_SERVER['SERVER_ADDR'] == '172.16.238.11' || $_SERVER['SERVER_ADDR'] == '10.0.11.198'){
            (new \Snowair\Debugbar\ServiceProvider())->start();
        }
        echo $this->handle()->getContent();
    }

    protected function registerServices()
    {
        $di = new FactoryDefault();

        // Specify routes for modules
        $di->setShared('router', function () {
            $router = new Router();
            $router->setUriSource($router::URI_SOURCE_SERVER_REQUEST_URI);
            include ('routes.php');
            return $router;
        });

        $di->setShared('baseConfig', function(){
            return include ABSOLUTE_DIR.'apps/config/config.php';
        });

        $this->di['app'] = $this;
        $this->setDI($di);
    }
}

$application = new Application();
$application->main();

?>