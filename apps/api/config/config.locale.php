<?php
setlocale ( LC_ALL, 'it_IT.UTF-8' );
date_default_timezone_set ( 'Europe/Rome' );

return new \Phalcon\Config ([
    'application' => [
        'controllersDir' 	=> __DIR__ . '/../controllers/',
        'modelsDir' 		=> __DIR__ . '/../../site/models/',
        'migrationsDir' 	=> __DIR__ . '/../migrations/',
        'viewsDir' 			=> __DIR__ . '/../views/',
        'siteViewsDir' 		=> ABSOLUTE_DIR . 'apps/site/views',
        'pluginsDir' 		=> __DIR__ . '/../plugins/',
        'libraryDir' 		=> __DIR__ . '/../library/',
        'formsDir' 			=> __DIR__ . '/../forms/',
        'cacheDir' 			=> __DIR__ . '/../cache/'
    ]
]);
