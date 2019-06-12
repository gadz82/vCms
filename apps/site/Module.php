<?php

namespace CMSIO\Site;

use apps\site\library\CmsTags;
use apps\site\library\Shortcodes;
use apps\site\library\Tags;
use apps\site\library\Translation;
use apps\site\plugins\CheckAuthPlugin;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use apps\site\library\Mailer;
use apps\site\plugins\SecurityPlugin;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Apc as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Http\Response\Cookies;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Cache\Frontend\Output as OutputFrontend;
use Phalcon\Cache\Backend\Apc as ApcBackend;
use apps\site\plugins\NotFoundPlugin;
use apps\site\plugins\CheckRoutePlugin;

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
            'apps\site\plugins' => __DIR__ . '/plugins/',
            'apps\site\library' => __DIR__ . '/library/',
            'apps\site\forms'   => __DIR__ . '/forms/'
        ]);

        $loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $di)
    {
        define('BASE_DIR', dirname(__DIR__));
        define('APP_DIR', BASE_DIR . '/site');

        $config = $di->get('baseConfig');
        if (file_exists(__DIR__ . '/config/config.php')) {
            $moduleConfig = include __DIR__ . '/config/config.php';
            $config->merge($moduleConfig);
        }
        $di->set('config', $config);
        $di->remove('baseConfig');

        define('APPLICATION_ENV', $config->application->appEnv);

        // EventsManager
        $di->setShared('dispatcher', function () use ($di) {
            $eventsManager = new EventsManager ();
            $eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin());
            $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin());
            $eventsManager->attach('dispatch:beforeExecuteRoute', new CheckRoutePlugin());

            $dispatcher = new Dispatcher ();
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });

        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $di->set('url', function () use ($config) {
            $url = new UrlResolver();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });

        /**
         * Setting up the view component
         */
        $di->set('view', function () use ($config) {

            $view = new View();
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
                    $compiler->addFilter('ceil', 'ceil');
                    $compiler->addFilter('floor', 'floor');
                    $compiler->addFunction('in_array', 'in_array');
                    $compiler->addFunction('array_key_exists', 'array_key_exists');
                    $compiler->addFunction('implode', 'implode');
                    $compiler->addFunction('fb', 'fb');
                    $compiler->addFunction('strtotime', 'strtotime');
                    $compiler->addFunction('substr', 'substr');
                    $compiler->addFunction('explode', 'explode');
                    $compiler->addFunction('str_replace', 'str_replace');
                    $compiler->addFunction('strpos', 'strpos');
                    $compiler->addFunction('min', 'min');
                    $compiler->addFunction('max', 'max');
                    $compiler->addFunction('nl2br', 'nl2br');
                    $compiler->addFunction('urlencode', 'urlencode');
                    $compiler->addFunction('date', 'date');
                    $compiler->addFunction('number_format', 'number_format');
                    return $volt;
                }
            ]);

            $eventsManager = new \Phalcon\Events\Manager();
            $eventsManager->attach("view:afterRenderView", function ($event, $view) {
                $search = [
                    '/\>[^\S ]+/s',
                    '/[^\S ]+\</s',
                    '/(\s)+/s',
                    '/<!--(.|\s)*?-->/'
                ];
                $replace = [
                    '>',
                    '<',
                    '\\1',
                    ''
                ];
                $buffer = preg_replace($search, $replace, $view->getContent());
                $view->setContent($buffer);
            });
            $view->setEventsManager($eventsManager);
            return $view;
        });

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $di->set('db', function () use ($config) {
            $dbConfig = $config->database->toArray();
            $adapter = $dbConfig ['adapter'];
            unset ($dbConfig ['adapter']);
            $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;
            return new $class ($dbConfig);
        });

        // ModelManager
        $di->set('modelsManager', function () {
            return new ModelManager();
        });

        // Metadata per MODEL -> APC
        $di->set('modelsMetadata', function () {
            return new MetaDataAdapter([
                'prefix'   => 'cmsio-metadata',
                'lifetime' => 86400
            ]);
        });

        /**
         * Start the session the first time some component request the session service
         */
        $di->set('session', function () {
            $session = new SessionAdapter ();
            $session->start();
            return $session;
        });

        // Gestione COOKIES
        $di->set('cookies', function () {
            $cookies = new Cookies ();
            $cookies->useEncryption(true);
            return $cookies;
        });

        // Configurazione FLASH con css custom
        $di->setShared('flash', function () {
            return new Flash([
                'error'   => 'alert alert-danger alert-dismissable m-lg-15',
                'success' => 'alert alert-success alert-dismissable m-lg-15',
                'notice'  => 'alert alert-info alert-dismissable m-lg-15',
                'plain'   => 'alert-plain alert-dismissable m-lg-15'
            ]);
        });

        // Configurazione FLASHSESSION con css custom
        $di->setShared('flashSession', function () {
            return new FlashSession([
                'error'   => 'alert alert-danger alert-dismissable m-lg-15',
                'success' => 'alert alert-success alert-dismissable m-lg-15',
                'notice'  => 'alert alert-info alert-dismissable m-lg-15',
                'plain'   => 'alert-plain alert-dismissable m-lg-15'
            ]);
        });

        // Configurazione CACHE per MODEL
        $di->set('modelsCache', function () {
            // Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Data([
                "lifetime" => 86400
            ]);
            // Memcached connection settings
            $cache = new ApcBackend ($frontCache, [
                'prefix' => 'cmsio-cache-'
            ]);
            return $cache;
        });

        $di->setShared('assets', new \apps\site\library\assets\Manager());

        // Set the views cache service
        $di->set('viewCache', function () {
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

        $di->set('auth', function () {
            return new \apps\site\library\Auth();
        });

        $di->set('mailer', function () {
            return new Mailer();
        });

        $di->set('tags', function () {
            return new Tags();
        });

        $di->set('shortcodes', function () {
            return new Shortcodes();
        });

        $di->set('__', function () {
            return new Translation();
        });

        $di->setShared('mDetect', function () {
            return new \Mobile_Detect();
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