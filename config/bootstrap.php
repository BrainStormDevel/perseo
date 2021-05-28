<?php

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use PerSeo\Handlers\HttpErrorHandler;
use PerSeo\Handlers\ShutdownHandler;

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Error vendor not found: You must use Composer to install dependencies";
    exit;
}

@require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions((file_exists(__DIR__ . '/settings.php') ? __DIR__ . '/settings.php' : __DIR__ . '/default.php'));
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$errorHandler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());
$shutdownHandler = new ShutdownHandler($request, $errorHandler, true);
register_shutdown_function($shutdownHandler);

// Register routes
(require __DIR__ . '/routes.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;
