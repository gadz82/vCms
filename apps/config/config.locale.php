<?php
setlocale(LC_ALL, 'it_IT.UTF-8');
date_default_timezone_set('Europe/Rome');

return new \Phalcon\Config([
    'debug'       => [
        'error_reporting' => E_ALL,
        'display_errors'  => 1,
        'tools'           => true,
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
        'controllersDir' 	=> ABSOLUTE_DIR . 'apps/admin/controllers/',
        'modelsDir' 		=> ABSOLUTE_DIR . 'apps/admin/models/',
        'migrationsDir' 	=> ABSOLUTE_DIR . 'apps/admin/migrations/',
        'viewsDir' 			=> ABSOLUTE_DIR . 'apps/admin/views/',
        'siteViewsDir' 		=> ABSOLUTE_DIR . 'apps/site/views',
        'pluginsDir' 		=> ABSOLUTE_DIR . 'apps/admin/plugins/',
        'libraryDir' 		=> ABSOLUTE_DIR . 'apps/admin/library/',
        'formsDir' 			=> ABSOLUTE_DIR . 'apps/admin/forms/',
        'cacheDir' 			=> ABSOLUTE_DIR . 'apps/admin/cache/',
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