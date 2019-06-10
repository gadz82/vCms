<?php
setlocale ( LC_ALL, 'it_IT.UTF-8' );
date_default_timezone_set ( 'Europe/Rome' );

return new \Phalcon\Config([
    'debug' => [
        'error_reporting' => E_ALL,
        'display_errors' => 1,
        'tools' => true,
        'apc' => true
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'cms',
        'password' => 'cms',
        'dbname' => 'cms',
        'charset' => 'utf8'
    ],
    'application' => [
        'appName'           => 'Verticalize',
        'appEnv'            => 'development',
        'controllersDir' 	=> __DIR__ . '/../controllers/',
        'modelsDir' 		=> __DIR__ . '/../models/',
        'migrationsDir' 	=> __DIR__ . '/../migrations/',
        'viewsDir' 			=> __DIR__ . '/../views/',
        'pluginsDir' 		=> __DIR__ . '/../plugins/',
        'libraryDir' 		=> __DIR__ . '/../library/',
        'formsDir' 			=> __DIR__ . '/../forms/',
        'cacheDir' 			=> __DIR__ . '/../cache/',
        'baseUri' 			=> '/',
        'siteUri'           => '//cms.loc',
        'protocol'          => 'https:',
        'defaultCode'       => 'it',
        'defaultId'         => '1',
        'multisite'         => true
    ],
    'mailer' => [
        'fromName' => 'VERTICALIZE',
        'fromEmail' => '',
        'method' => 'smtp', //mail
        'smtp' => [
            'server' => '',
            'port' => 465,
            'security' => 'ssl',
            'username' => '',
            'password' => 'ttt'
        ]
    ],
    'facebook' => [
        'appId' => '',
        'appSecret' => '',
        'cbPage' => '/user/facebookRegistration'
    ],
    'sessionKey' => 'verticalize-local',
    'sessionKeyAdmin' => 'auth-identity',
    'context' => 'dev'
]);