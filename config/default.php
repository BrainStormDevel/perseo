<?php

return [
    'settings.determineRouteBeforeAppMiddleware' => true,
    'settings.displayErrorDetails'               => true,
    'settings.addContentLengthHeader'            => false,
	'settings.logger' => [
		'critical' => [
			'name' => 'Critical',
			'path' => __DIR__ . '/../logs/critical.log',
			'level' => \Monolog\Logger::CRITICAL,
		],
		'error' => [
			'name' => 'Error',
			'path' => __DIR__ . '/../logs/errors.log',
			'level' => \Monolog\Logger::ERROR,
		],
		'warning' => [
			'name' => 'Warning',
			'path' => __DIR__ . '/../logs/warnings.log',
			'level' => \Monolog\Logger::WARNING,
		],
		'notice' => [
			'name' => 'Notice',
			'path' => __DIR__ . '/../logs/notices.log',
			'level' => \Monolog\Logger::NOTICE,
		]		
	]
];
