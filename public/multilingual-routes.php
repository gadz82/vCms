<?php
$router->setDefaultModule("site");

/**
 * Frontend
 */
$router->add('/', array(
    'module' => "site",
    'controller' => 'index',
    'action' => 'index'
))->setName('Home')->beforeMatch(function(){
    return \Phalcon\DI::getDefault()->getResponse()->redirect('/it', true, 301);
});

$router->add('/([a-z]{2})', array(
    'module' => "site",
    'controller' => 'index',
    'action' => 'index',
    'application' => 1
))->setName('Home');

$router->add('/404')->beforeMatch(function(){
    return \Phalcon\DI::getDefault()->getResponse()->redirect('/it/404', true, 301);
});

$router->add('/([a-z]{2})/404', array(
    'module' => "site",
    'controller' => 'errors',
    'action' => "show404",
    'application' => 1
))->setName('Api Root');

$router->add('/([a-z]{2})/{post_type_slug:[a-z\-]+}/', array(
    'module' => "site",
    'controller' => 'list',
    'action' => 'list',
    'post_type_slug' => 2,
    'application' => 1
))->setName('Listing Post Type');

$router->add('/([a-z]{2})/{post_type_slug:[a-z\-]+}/:action/:params', array(
    'module' => "site",
    'controller' => 'list',
    'action' => 3,
    'post_type_slug' => 2,
    'params' => 4,
    'application' => 1
))->setName('Listing Post Type with Filters');

$router->add('/([a-z]{2})/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}', array(
    'module' => "site",
    'controller' => 'entity',
    'action' => 'read',
    'post_type_slug' => 1,
    'post_slug' => 2,
    'application' => 1
))->setName('Dettaglio');

$router->add('/([a-z]{2})/{post_type_slug:[a-z\-]+}/{post_slug:[a-z0-9\-]+}.pdf', array(
    'module' => "site",
    'controller' => 'pdf',
    'action' => 'read',
    'post_type_slug' => 2,
    'post_slug' => 3,
    'application' => 1
))->setName('Scheda PDF');

$router->add('/([a-z]{2})/ajax/:action/:params', array(
    'module' => "site",
    'controller' => 'ajax',
    'action' => 2,
    'params' => 3,
    'application' => 1
))->setName('Dettaglio');

$router->add('/([a-z]{2})/forms/:action/:params', array(
    'module' => "site",
    'controller' => 'forms',
    'action' => 1,
    'params' => 2,
    'application' => 1
))->setName('Request form');

/**
 * Sitemap
 */
$router->add('/([a-z]{2})/sitemap.xml', array(
    'module'	=> 'site',
    'controller' => 'sitemap',
    'action' => 'index',
    'application' => 1
))->setName('Sitemap');

/**
 * Admin
 */
$router->add("/login", array(
    'module'     => 'admin',
    'controller' => 'session',
    'action'     => 'index'
))->setName('Login');

$router->add('/admin', array(
    'module' => "admin",
    'controller' => 'session',
    'action' => "index"
))->setName('Admin Root');

$router->add('/admin/:controller', array(
    'module' => "admin",
    'controller' => 1,
    'action' => "index"
))->setName('Admin Index of Controller');

$router->add('/admin/:controller/:action/', array(
    'module' => "admin",
    'controller' => 1,
    'action' => 2
))->setName('Admin Specific Action');

$router->add('/admin/:controller/:action/:params', array(
    'module' => "admin",
    'controller' => 1,
    'action' => 2,
    'params' => 3
))->setName('Admin Specific Action with params');


/**
 * 404
 */
$router->notFound(
    [
        "controller" => "errors",
        "action"     => "show404",
    ]
);