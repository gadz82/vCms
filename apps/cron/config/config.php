<?php
setlocale ( LC_ALL, 'it_IT.UTF-8' );
date_default_timezone_set ( 'Europe/Rome' );
if (file_exists(__DIR__ . '/config.locale.php')) {
    return include(__DIR__ . '/config.locale.php');
} elseif (file_exists(__DIR__ . '/config.staging.php')) {
    return include(__DIR__ . '/config.staging.php');
} else {
    return new \Phalcon\Config([
        'application' => [
            'controllersDir' 	=> __DIR__ . '/../controllers/',
            'modelsDir' 		=> __DIR__ . '/../../site/models/',
            'viewsDir' 			=> __DIR__ . '/../views/',
            'pluginsDir' 		=> __DIR__ . '/../plugins/',
            'libraryDir' 		=> __DIR__ . '/../library/',
            'formsDir' 			=> __DIR__ . '/../forms/',
            'cacheDir' 			=> __DIR__ . '/../cache/',
        ]
    ]);
}
