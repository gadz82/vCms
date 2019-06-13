<?php
setlocale(LC_ALL, 'it_IT.UTF-8');
date_default_timezone_set('Europe/Rome');
return new \Phalcon\Config ([
    'debug'       => [
        'error_reporting' => E_ALL,
        'display_errors'  => 1,
        'tools'           => true,
        'apc'             => true
    ]
]);