<?php

// Error reporting for production
error_reporting(0);
ini_set('display_errors', '0');

// Timezone
date_default_timezone_set('Europe/Berlin');

return [
	'settings_default' => 1,
    'settings_global' => [
        'template' => 'default',
        'locale' => false,
        'language' => 'en',
        'languages' => ['it', 'en']
    ],
    'settings_root'     => realpath(__DIR__ .'/..'),
    'settings_temp' => realpath(__DIR__ .'/../tmp'),
    'settings_modules' => realpath(__DIR__ .'/../modules'),
    'settings_error' => [
        'reporting' => ['E_ALL', '~E_NOTICE'],
        'display_error_details' => true,
        'log_errors' => true,
        'log_error_details' => true
    ],
    'settings_session' =>[
        'name' => 'client',
        'cache_expire' => 0,
    ],
    'settings_twig' => [
        // Template paths
        'paths' => [
            realpath(__DIR__ .'/../templates'),
        ],
        'debug' => true,
        'path' => realpath(__DIR__ .'/../cache'),
        'url_base_path' => 'cache/',
        // Cache settings
        'cache_enabled' => false,
        'cache_path' => realpath(__DIR__ .'/../tmp'),
        'cache_name' => 'assets-cache',
        //  Should be set to 1 (enabled) in production
        'minify' => 0,
    ],
    'settings_logger' => [
        'name' => 'perseo',
        'path' => realpath(__DIR__ .'/../logs'),
        'filename' => 'perseo.log',
        'level' => \Monolog\Logger::DEBUG,
        'file_permission' => 0775,
    ]
];
