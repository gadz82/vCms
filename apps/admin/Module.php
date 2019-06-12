<?php
namespace CMSIO\Admin;

use Phalcon\Loader;
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
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Forms\Manager as FormManager;
use Phalcon\Http\Response\Cookies;
use Phalcon\Crypt;
use Phalcon\Cache\Frontend\Output as OutputFrontend;
use Phalcon\Cache\Backend\Apc as ApcBackend;
use Phalcon\Security;
use apps\admin\plugins\SecurityPlugin;
use apps\admin\plugins\NotFoundPlugin;
use apps\admin\plugins\CheckRoutePlugin;
use apps\admin\library\Mailer;
use Phalcon\Text;

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
            __DIR__ . '/models/'

        ])->registerNamespaces([
            'apps\admin\plugins' => __DIR__ . '/plugins/',
            'apps\admin\library' => __DIR__ . '/library/',
            'apps\admin\forms'   => __DIR__ . '/forms/'
        ]);
        $loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $di)
    {

        define('BASE_DIR', dirname(__DIR__));
        define('APP_DIR', BASE_DIR . '/admin');

        $config = $di->get('baseConfig');
        if (file_exists(__DIR__ . '/config/config.php')) {
            $moduleConfig = include __DIR__ . '/config/config.php';
            $config->merge($moduleConfig);
        }
        $di->set('config', $config);
        $di->remove('baseConfig');

        define('APPLICATION_ENV', $config->application->appEnv);

        if ($config->application->appEnv) $di->setShared('config', $config);

        // EventsManager
        $di->setShared('dispatcher', function () use ($di) {
            $public_controllers = [
                'session' => [
                    'index',
                    'login'
                ],
                'errors'  => [
                    'show401',
                    'show404',
                    'show500'
                ],
                'Asset'   => [
                    'js',
                    'css'
                ]
            ];

            $eventsManager = new EventsManager ();
            $eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin($public_controllers));
            $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin());
            $eventsManager->attach('dispatch:beforeExecuteRoute', new CheckRoutePlugin());

            $dispatcher = new Dispatcher ();
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });

        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $di->setShared('url', function () use ($config) {
            $url = new UrlResolver ();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });

        /**
         * Setting up the view component
         */
        $di->setShared('view', function () use ($config) {
            $view = new View ();
            $view->setViewsDir($config->application->viewsDir);
            $view->registerEngines([
                '.volt' => function ($view, $di) use ($config) {

                    $volt = new VoltEngine ($view, $di);

                    $volt->setOptions([
                        'compiledPath'      => $config->application->cacheDir,
                        'compiledSeparator' => '_'
                    ]);

                    $compiler = $volt->getCompiler();
                    $compiler->addFilter('round', 'round');
                    $compiler->addFunction('in_array', 'in_array');
                    $compiler->addFunction('array_key_exists', 'array_key_exists');
                    $compiler->addFunction('implode', 'implode');
                    $compiler->addFunction('fb', 'fb');
                    $compiler->addFunction('strtotime', 'strtotime');
                    $compiler->addFunction('substr', 'substr');
                    $compiler->addFunction('str_replace', 'str_replace');
                    $compiler->addFunction('strpos', 'strpos');
                    $compiler->addFunction('date', 'date');
                    $compiler->addFunction('strtotime', 'strtotime');
                    $compiler->addFunction('camelize', function ($str) {
                        return Text::camelize($str);
                    });
                    return $volt;
                }
            ]);

            return $view;
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

        // Session manager
        $di->setShared('session', function () {
            $session = new SessionAdapter ();
            $session->start();
            return $session;
        });

        // Gestione COOKIES
        $di->setShared('cookies', function () {
            $cookies = new Cookies ();
            $cookies->useEncryption(true);
            return $cookies;
        });

        // Configurazione CRYPT
        $di->setShared('crypt', function () {
            $crypt = new Crypt ();
            $crypt->setKey('1E3#xJ71([k?J-Y');
            return $crypt;
        });

        // Configuarazione SECURITY
        $di->setShared('security', function () {
            $security = new Security ();
            // Set the password hashing factor to 12 rounds
            $security->setWorkFactor(12);
            return $security;
        });

        // Configurazione FLASH con css custom
        $di->setShared('flash', function () {
            return new Flash ([
                'error'   => 'alert alert-danger alert-dismissable',
                'success' => 'alert alert-success alert-dismissable',
                'notice'  => 'alert alert-info alert-dismissable',
                'plain'   => 'alert-plain alert-dismissable'
            ]);
        });

        // Configurazione FLASHSESSION con css custom
        $di->setShared('flashSession', function () {
            return new FlashSession([
                'error'   => 'alert alert-danger alert-dismissable',
                'success' => 'alert alert-success alert-dismissable',
                'notice'  => 'alert alert-info alert-dismissable',
                'plain'   => 'alert-plain alert-dismissable'
            ]);
        });

        // Configurazione CACHE per MODEL
        $di->setShared('modelsCache', function () {
            // Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Data([
                "lifetime" => 86400
            ]);
            // Memcached connection settings
            $cache = new ApcBackend($frontCache, [
                'prefix' => 'cms-cache-'
            ]);
            return $cache;
        });

        // Set the views cache service
        $di->setShared('viewCache', function () {
            // Cache data for one day by default
            $frontCache = new OutputFrontend([
                "lifetime" => 86400
            ]);

            // Memcached connection settings
            $cache = new ApcBackend($frontCache, [
                'lifetime' => 86400
            ]);
            return $cache;
        });

        $di->setShared('forms', function () {
            return new FormManager ();
        });

        $di->set('auth', function () {
            return new \apps\admin\library\Auth();
        });

        $di->setShared('mailer', function () {
            return new Mailer();
        });

        $di->setShared('excel', function () {
            require_once __DIR__ . '/library/PHPExcel.php';
            return new \PHPExcel();
        });

        $di->set('menu', function () {
            return new \apps\admin\library\Menu();
        });

        $di->setShared('uploader', function () {
            return new \apps\admin\library\UploadHandler();
        });

        if ($config->debug->tools) {
            if ($config->debug->error_reporting !== 0) error_reporting($config->debug->error_reporting);
            if ($config->debug->display_errors !== 0) ini_set('display_errors', $config->debug->display_errors);
        }

        if ($di->has('debugbar')) {
            $debugbar = $di['debugbar'];
            if ($config->debug->tools || $di['session']->has('debug')) {
                $debugbar->attachDb('db');
                $debugbar->enable();
            } else {
                $debugbar->disable();
            }
        }
    }
}