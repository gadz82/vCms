<?php
setlocale(LC_ALL, 'it_IT.UTF-8');
date_default_timezone_set('Europe/Rome');

return new \Phalcon\Config([
    'debug'       => [
        'error_reporting' => E_ALL,
        'display_errors'  => 1,
        'tools'           => false,
        'apc'             => true
    ],
    'database'    => [
        'adapter'  => 'Mysql',
        'host'     => '127.0.0.1',
        'username' => 'cms',
        'password' => 'cms',
        'dbname'   => 'cms',
        'charset'  => 'utf8'
    ],
    'application' => [
        'appName'        => 'VERTICALIZE',
        'appEnv'         => 'development',
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir'      => __DIR__ . '/../../site/models/',
        'viewsDir'       => __DIR__ . '/../views/',
        'pluginsDir'     => __DIR__ . '/../plugins/',
        'libraryDir'     => __DIR__ . '/../library/',
        'formsDir'       => __DIR__ . '/../forms/',
        'cacheDir'       => __DIR__ . '/../cache/',
        'baseUri'        => '/',
        'siteUri'        => '//verticalize.loc',
        'protocol'       => 'https:',
        'multisite'      => true,
        'defaultId'      => '1',
        'defaultCode'    => 'it'
    ],
    'mailer'      => [
        'fromName'  => 'VERTICALIZE',
        'fromEmail' => '',
        'method'    => 'smtp', //mail
        'smtp'      => [
            'server'   => '',
            'port'     => 465,
            'security' => 'ssl',
            'username' => '',
            'password' => 'ttt'
        ]
    ]
]);