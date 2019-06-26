<?php

return [
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => true,
    'settings.addContentLengthHeader' => false,
    'settings.global' => [
        'sitename' => 'Sito di Prova',
        'encoding' => 'utf-8',
        'template' => 'default',
        'locale' => true,
        'language' => 'en',
        'languages' => ['it', 'en']
    ],
    'settings.secure' => [
        'crypt_salt' => 'edecd098c2eb3b2515e514247e09ad8db72eb50e7ab2340a85ccdfd28f759a19',
        'max_a' => '200',
        'max_u' => '16',
        'max_p' => '20',
        'max_e' => '40',
        'max_t' => '100'
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
    'settings.database' => [
        'default' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'perseo',
            'username' => 'root',
            'password' => '123456',
            'prefix' => 'lvjrh_',
            'charset' => 'utf8',
            'port' => 3306

        ]
    ]
];