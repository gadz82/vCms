<?php
$router->setDefaultModule("site");
$config = include __DIR__.'/../apps/config/config.php';

$key = $config->application->appCode.'.Routes';
$rs = apcu_fetch ( $key );
if (! $rs) {
    $con = new Phalcon\Db\Adapter\Pdo\Mysql([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ]);

    $con->connect();

    $query = "
        SELECT 
            a.id AS id_app,
            a.`codice` AS codice_app,
            tr.`metodo` AS method,
            ar.`path` AS route,
            ar.`params`,
            ar.`nome` AS route_name
        FROM
            applicazioni_routes ar
        INNER JOIN `applicazioni` a ON a.id = ar.`id_applicazione` AND a.`attivo` = 1
        INNER JOIN `tipologie_routes` tr ON tr.`id` = ar.`id_tipologia_route` AND tr.`attivo` = 1
        WHERE
            ar.id_tipologia_stato = 1
        AND
            ar.attivo = 1
        ORDER BY a.id, ar.`ordine` ASC
    ";
    $rs = $con->fetchAll($query, \Phalcon\Db::FETCH_OBJ);
    if(!$rs || count($rs) == 0) die('Errore CMS Desengo');
    apcu_store ( $key, $rs );
}

$nr = count($rs);
$routes = [];
for($i = 0; $i < $nr; $i++){
    $route = $rs[$i];
    $path = $route->id_app == $config->application->defaultId ? $route->route : DIRECTORY_SEPARATOR.$route->codice_app.$route->route;
    $params = json_decode($route->params, true);
    $params['application'] = $route->codice_app;
    $routes[$path] = $params;
    $router->{$route->method}($path, $params)->setName($route->route_name);
}

/**
 * Frontend
 */
/*
$router->add('/', [
    'module' => "site",
    'controller' => 'index',
    'action' => 'index'
])->setName('Home');

$router->add('/404', [
    'module' => "site",
    'controller' => 'errors',
    'action' => "show404"
])->setName('Api Root');

$router->add('/{post_slug:[a-z\-]+}', [
    'module' => "site",
    'controller' => 'entity',
    'action' => 'read',
    'post_type_slug' => 'pagina',
    'params' => 1
])->setName('Listing Post Type');


$router->add('/{post_type_slug:[a-z\-]+}/', [
    'module' => "site",
    'controller' => 'list',
    'action' => 'list',
    'post_type_slug' => 1
])->setName('Listing Post Type');

$router->add('/{post_type_slug:[a-z\-]+}/:action/:params', [
    'module' => "site",
    'controller' => 'list',
    'action' => 2,
    'post_type_slug' => 1,
    'params' => 3
])->setName('Listing Post Type with Filters');

$router->add('/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}', [
    'module' => "site",
    'controller' => 'entity',
    'action' => 'read',
    'post_type_slug' => 1,
    'post_slug' => 2
])->setName('Dettaglio');

$router->add('/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}.pdf', [
    'module' => "site",
    'controller' => 'pdf',
    'action' => 'read',
    'post_type_slug' => 1,
    'post_slug' => 2
])->setName('Scheda PDF');

/*$router->add('/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}.amp', [
    'module' => "site",
    'controller' => 'amp',
    'action' => 'read',
    'post_type_slug' => 1,
    'post_slug' => 2
])->setName('Dettaglio AMP');*/
/*
$router->add('/user', [
    'module' => "site",
    'controller' => 'users',
    'action' => 'index'
])->setName('Users');

$router->add('/user/:action', [
    'module' => "site",
    'controller' => 'users',
    'action' => 1
])->setName('Users Action');

$router->add('/user/:action/:params', [
    'module' => "site",
    'controller' => 'users',
    'action' => 1,
    'params' => 2
])->setName('Users Action with params');

$router->add('/ajax/:action/:params', [
    'module' => "site",
    'controller' => 'ajax',
    'action' => 1,
    'params' => 2
])->setName('Dettaglio');

$router->add('/forms/:action/:params', [
    'module' => "site",
    'controller' => 'forms',
    'action' => 1,
    'params' => 2
])->setName('Request form');

$router->add('/media/:action/:params', [
    'module' => "site",
    'controller' => 'media',
    'action' => 1,
    'params' => 2
])->setName('Render Media');
*/
/**
 * Sitemap
 */
/*
$router->add('/sitemap.xml', [
    'module'	=> 'site',
    'controller' => 'sitemap',
    'action' => 'index'
])->setName('Sitemap');
*/

/**
 * Admin
 */
$router->add("/login", [
    'module'     => 'admin',
    'controller' => 'session',
    'action'     => 'index'
])->setName('Login');

$router->add('/admin', [
    'module' => "admin",
    'controller' => 'session',
    'action' => "index"
])->setName('Admin Root');

$router->add('/admin/:controller', [
    'module' => "admin",
    'controller' => 1,
    'action' => "index"
])->setName('Admin Index of Controller');

$router->add('/admin/:controller/:action/', [
    'module' => "admin",
    'controller' => 1,
    'action' => 2
])->setName('Admin Specific Action');

$router->add('/admin/:controller/:action/:params', [
    'module' => "admin",
    'controller' => 1,
    'action' => 2,
    'params' => 3
])->setName('Admin Specific Action with params');


/**
 * Api v1
 */
$router->add('/api/v1', [
    'module' => "api",
    'controller' => 'api',
    'action' => "index"
])->setName('Api Root');

$router->add('/api/v1/:controller', [
    'module' => "api",
    'controller' => 1,
    'action' => "index"
])->setName('Api Index of Controller');

$router->add('/cron/:controller/:action/', array(
    'module' => "cron",
    'controller' => 1,
    'action' => 2
))->setName('cron Specific Action');


$router->add('/api/v1/entities/{post_type_slug:[a-z\-]+}/', [
    'module' => "api",
    'controller' => 'list',
    'action' => 'fetch',
    'post_type_slug' => 1
])->setName('Listing Post Type');

$router->add('/api/v1/entities/{post_type_slug:[a-z\-]+}/:action/:params', [
    'module' => "api",
    'controller' => 'list',
    'action' => 2,
    'post_type_slug' => 1,
    'params' => 3
])->setName('Listing Post Type with Filters');

$router->add('/api/v1/entities/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}', [
    'module' => "api",
    'controller' => 'entity',
    'action' => 'read',
    'post_type_slug' => 1,
    'post_slug' => 2
])->setName('Dettaglio');


/**
 * Cron
 */
$router->add('/cron/:controller/:action/', [
    'module' => "cron",
    'controller' => 1,
    'action' => 2
])->setName('Cron Specific Action');

$router->add('/cron/:controller/:action/:params', [
    'module' => "cron",
    'controller' => 1,
    'action' => 2,
    'params' => 3
])->setName('Cron Specific Action with params');


/**
 * 404
 */
$router->notFound(
    [
        "controller" => "errors",
        "action"     => "show404",
    ]
);