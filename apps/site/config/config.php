<?php
setlocale(LC_ALL, 'it_IT.UTF-8');
date_default_timezone_set('Europe/Rome');
if (file_exists(__DIR__ . '/config.locale.php')) {
    return include(__DIR__ . '/config.locale.php');
} elseif (file_exists(__DIR__ . '/config.staging.php')) {
    return include(__DIR__ . '/config.staging.php');
} else {

    return new \Phalcon\Config([
        'debug' => [
            'error_reporting' => E_ALL,
            'display_errors' => 1,
            'tools' => true,
            'apc' => true
        ],
        'application' => [
            'controllersDir' 	=> __DIR__ . '/../controllers/',
            'modelsDir' 		=> __DIR__ . '/../models/',
            'migrationsDir' 	=> __DIR__ . '/../migrations/',
            'viewsDir' 			=> __DIR__ . '/../views/',
            'pluginsDir' 		=> __DIR__ . '/../plugins/',
            'libraryDir' 		=> __DIR__ . '/../library/',
            'formsDir' 			=> __DIR__ . '/../forms/',
            'cacheDir' 			=> __DIR__ . '/../cache/',
        ],
        'facebook' => [
            'appId' => '',
            'appSecret' => '',
            'cbPage' => '/user/facebookRegistration'
        ],
        'sessionKey' => 'verticalize-prod',
        'sessionKeyAdmin' => 'auth-identity'
    ]);
}
