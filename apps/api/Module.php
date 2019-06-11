<?php
namespace CMSIO\Api;


use apps\api\library\Mailer;
use apps\api\plugins\CheckAuthPlugin;
use apps\api\plugins\ExceptionHandlerPlugin;

use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Apc as MetaDataAdapter;

use Phalcon\Cache\Backend\Apc as ApcBackend;

use apps\api\plugins\CheckRoutePlugin;

class Module implements ModuleDefinitionInterface
{
    /**
     * Register a specific autoloader for the module
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();
        $loader->registerDirs ( array (
        		__DIR__.'/controllers/',
        		__DIR__.'/../site/models/'

        ) )->registerNamespaces ( array (
        		'apps\api\plugins' => __DIR__.'/plugins/',
        		'apps\api\library' => __DIR__.'/library/',
                'apps\admin\plugins' => __DIR__.'/../admin/plugins/',
                'apps\admin\library' => __DIR__.'/../admin/library/'
        ) );
        

        $loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $di)
    {

    	define ( 'BASE_DIR', dirname ( __DIR__ ) );
    	define ( 'APP_DIR', BASE_DIR . '/api' );

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }


        $config = $di->get('baseConfig');
        if(file_exists(__DIR__.'/config/config.php')){
            $moduleConfig = include __DIR__.'/config/config.php';
            $config->merge($moduleConfig);
        }
        $di->set ( 'config', $config );
        $di->remove('baseConfig');

        define('APPLICATION_ENV', $config->application->appEnv);


		// EventsManager
		$di->setShared ( 'dispatcher', function () use($di) {

			$eventsManager = new EventsManager ();
			$eventsManager->attach ( 'dispatch:beforeException', new ExceptionHandlerPlugin() );
            $eventsManager->attach ( 'dispatch:beforeExecuteRoute', new CheckAuthPlugin() );
			$eventsManager->attach ( 'dispatch:beforeExecuteRoute', new CheckRoutePlugin() );
            $eventsManager->attach ( 'application:beforeSendResponse',  function(\Phalcon\Events\Event $event, Application $app, Response $response){
                $response->setContentType('application/json', 'UTF-8');
                if($app->request->get('callback')){
                    $response->setContent($app->request['callback'].'('.$response->getContent().')');
                }
            });

			$dispatcher = new Dispatcher ();
			$dispatcher->setEventsManager ( $eventsManager );
			return $dispatcher;
		} );

		/**
		 * Database connection is created based in the parameters defined in the configuration file
		 */
		$di->setShared ( 'db', function () use($config) {
			$dbConfig = $config->database->toArray ();
			$adapter = $dbConfig ['adapter'];
			unset ( $dbConfig ['adapter'] );

			$class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

			return new $class ( $dbConfig );
		} );

		// ModelManager
		$di->setShared ( 'modelsManager', function () {
			return new ModelManager ();
		} );

		// Metadata per MODEL -> APC
		$di->setShared ( 'modelsMetadata', function () {
			return new MetaDataAdapter ( array (
					'prefix' => 'cmsio-metadata',
					'lifetime' => 86400
			) );
		} );
        /**
         * Setting up the view component
         */
        $di->set ( 'view', function () use($config) {

            $view = new View ();

            $view->setViewsDir ( $config->application->viewsDir );

            $view->registerEngines ( array (
                '.phtml' => function ($view, $di) use($config) {

                    $volt = new VoltEngine ( $view, $di );
                    $volt->setOptions ( array (
                        'compiledPath' => $config->application->cacheDir,
                        'compiledSeparator' => '_'
                    ) );
                    return $volt;
                }
            ));

            return $view;
        } );

		// Configurazione CACHE per MODEL
		$di->setShared ( 'modelsCache', function () {

			// Cache data for one day by default
			$frontCache = new \Phalcon\Cache\Frontend\Data ( array (
					"lifetime" => 86400
			) );

			// Memcached connection settings
			$cache = new ApcBackend ( $frontCache, array (
					'prefix' => 'cms-cache-'
			) );

			return $cache;
		} );
		$di->setShared ( 'mailer', function () {
			return new Mailer();
		} );
    }
}