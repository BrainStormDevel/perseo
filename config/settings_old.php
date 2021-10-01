<?php

return [
    'settings.global' => [
        'sitename' => 'Sito di Prova',
        'encoding' => 'utf-8',
		'template' => 'default',
		'locale' => true,
		'maintenance' => false,
		'maintenancekey' => 'ukeweaj5a2g9',
        'language' => 'it',
		'languages' => ['it', 'en']
    ],
    'settings.root' => realpath(__DIR__ .'/..'),
    'settings.temp' => realpath(__DIR__ .'/../tmp'),
	'settings.modules' =>  realpath(__DIR__ .'/../modules'),
    'settings.error' => [
        'display_error_details' => false,
		'log_errors' => true,
        'log_error_details' => true
    ],
	'settings.session' =>[
        'name' => 'client',
        'cache_expire' => 0,
	],
	'settings.twig' => [
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
	'settings.logger' => [
		'name' => 'app',
		'path' => realpath(__DIR__ .'/../logs'),
		'filename' => 'dgortmce_',
		'level' => \Monolog\Logger::DEBUG,
		'file_permission' => 0775,
	],
    'settings.secure' => [
        'crypt_salt' => '7be86cbf4947c082fc24bf6774d72fd8c5bee0bcd3b7a9321cc76f94c16a3b47'
    ],
    'settings.cookie' => [
		'admin' => 'ADM',
		'user' => 'USR',
        'cookie_exp' => '3600',
        'cookie_max_exp' => '7889238',
        'cookie_path' => '/perseo/',
        'cookie_secure' => false,
        'cookie_http' => true
    ],
    'settings.db' => [
        'default' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'perseo',
            'username' => 'root',
            'password' => '123456',
            'prefix' => 'vumxm_',
            'charset' => 'utf8mb4',
            'port' => 3306

        ]
    ]
];