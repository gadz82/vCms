<?php
$router->setDefaultModule("site");
$config = include __DIR__.'/../apps/config/config.php';

$key = $config->application->appName.'.Routes';
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
    if(!$rs || count($rs) == 0) die('Errore CMS Verticalize');
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