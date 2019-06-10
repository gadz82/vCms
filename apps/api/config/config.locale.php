<?php
setlocale ( LC_ALL, 'it_IT.UTF-8' );
date_default_timezone_set ( 'Europe/Rome' );

return new \Phalcon\Config ( array (
    'debug' => array (
        'error_reporting' => E_ALL,
        'display_errors' => 1,
        'tools' => false,
        'apc' => true
    ),
    'database' => array (
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ),
    'application' => array (
        'appName'           => 'VERTICALIZE',
        'appEnv'            => 'development',
        'controllersDir' 	=> __DIR__ . '/../controllers/',
        'modelsDir' 		=> __DIR__ . '/../../site/models/',
        'migrationsDir' 	=> __DIR__ . '/../migrations/',
        'viewsDir' 			=> __DIR__ . '/../views/',
        'pluginsDir' 		=> __DIR__ . '/../plugins/',
        'libraryDir' 		=> __DIR__ . '/../library/',
        'formsDir' 			=> __DIR__ . '/../forms/',
        'cacheDir' 			=> __DIR__ . '/../cache/',
        'baseUri' 			=> '/',
        'siteUri'           => '//verticalize.loc',
        'protocol'          => 'https:',
        'multisite'         => true,
        'defaultId'         => '1',
        'defaultCode'       => 'it'
    ),
    'mailer' => array (
        'fromName' => 'VERTICALIZE',
        'fromEmail' => '',
        'method' => 'smtp', //mail
        'smtp' => array (
            'server' => '',
            'port' => 465,
            'security' => 'ssl',
            'username' => '',
            'password' => 'ttt'
        )
    )
) );