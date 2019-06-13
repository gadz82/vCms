<?php
setlocale(LC_ALL, 'it_IT.UTF-8');
date_default_timezone_set('Europe/Rome');
return new \Phalcon\Config ([
    'debug'       => [
        'error_reporting' => E_ALL,
        'display_errors'  => 1,
        'tools'           => false,
        'apc'             => true
    ],
    'application' => [
        'modelsDir'      => __DIR__ . '/../models/',
        'migrationsDir'  => __DIR__ . '/../migrations/',
        'controllersDir' => __DIR__ . '/../controllers/',
        'siteViewsDir'   => __DIR__ . '/../../site/views/',
        'viewsDir'       => __DIR__ . '/../views/',
        'pluginsDir'     => __DIR__ . '/../plugins/',
        'libraryDir'     => __DIR__ . '/../library/',
        'formsDir'       => __DIR__ . '/../forms/',
        'cacheDir'       => __DIR__ . '/../cache/',
    ]
]);