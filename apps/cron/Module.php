<?php
namespace CMSIO\Cron;

use apps\cron\plugins\ExceptionHandlerPlugin;

use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Apc as MetaDataAdapter;

use Phalcon\Cache\Backend\Apc as ApcBackend;

use apps\cron\plugins\CheckRoutePlugin;

class Module implements ModuleDefinitionInterface
{
    /**
     * Register a specific autoloader for the module
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();
        $loader->registerDirs([
            __DIR__ . '/controllers/',
            __DIR__ . '/../admin/models/'

        ])->registerNamespaces([
            'apps\cron\plugins'   => __DIR__ . '/plugins/',
            'apps\cron\library'   => __DIR__ . '/library/',
            'apps\admin\plugins'  => __DIR__ . '/../admin/plugins/',
            'apps\admin\library'  => __DIR__ . '/../admin/library/',
            'apps\cron\exception' => __DIR__ . '/../cron/exception/'
        ]);

        $loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $di)
    {

        define('BASE_DIR', dirname(__DIR__));
        define('APP_DIR', BASE_DIR . '/cron');

        $config = $di->get('baseConfig');
        if (file_exists(__DIR__ . '/config/config.php')) {
            $moduleConfig = include __DIR__ . '/config/config.php';
            $config->merge($moduleConfig);
        }
        $di->set('config', $config);
        $di->remove('baseConfig');

        // EventsManager
        $di->setShared('dispatcher', function () use ($di) {

            $eventsManager = new EventsManager ();
            $eventsManager->attach('dispatch:beforeException', new ExceptionHandlerPlugin());
            $eventsManager->attach('dispatch:beforeExecuteRoute', new CheckRoutePlugin());
            $eventsManager->attach('application:beforeSendResponse', function (\Phalcon\Events\Event $event, Application $app, Response $response) {
                $response->setContentType('application/json', 'UTF-8');
                if ($app->request->get('callback')) {
                    $response->setContent($app->request['callback'] . '(' . $response->getContent() . ')');
                }
            });

            $dispatcher = new Dispatcher ();
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $di->setShared('db', function () use ($config) {
            $dbConfig = $config->database->toArray();
            $adapter = $dbConfig ['adapter'];
            unset ($dbConfig ['adapter']);

            $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

            return new $class ($dbConfig);
        });

        // ModelManager
        $di->setShared('modelsManager', function () {
            return new ModelManager ();
        });

        // Metadata per MODEL -> APC
        $di->setShared('modelsMetadata', function () {
            return new MetaDataAdapter ([
                'prefix'   => 'cmsio-metadata',
                'lifetime' => 86400
            ]);
        });
        /**
         * Setting up the view component
         */
        $di->set('view', function () use ($config) {

            $view = new View ();

            $view->setViewsDir($config->application->viewsDir);

            $view->registerEngines([
                '.phtml' => function ($view, $di) use ($config) {

                    $volt = new VoltEngine ($view, $di);
                    $volt->setOptions([
                        'compiledPath'      => $config->application->cacheDir,
                        'compiledSeparator' => '_'
                    ]);
                    return $volt;
                }
            ]);

            return $view;
        });
        // Configurazione CACHE per MODEL
        $di->setShared('modelsCache', function () {

            // Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Data ([
                "lifetime" => 86400
            ]);

            // Memcached connection settings
            $cache = new ApcBackend ($frontCache, [
                'prefix' => 'cms-cache-'
            ]);

            return $cache;
        });

        /**
         * Start the session the first time some component request the session service
         */
        $di->setShared('session', function () {
            $session = new SessionAdapter ();
            $session->start();

            return $session;
        });

    }
}
