<?php

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Http\ServerRequest as DecoratedServerRequest;
use PerSeo\Handlers\HttpErrorHandler;
use PerSeo\Handlers\ShutdownHandler;
use Psr\Log\LoggerInterface;

function basicshutdownFunction() {
    $error = error_get_last();
    if ($error !== null) {
        echo "Error: {$error['message']} in {$error['file']} on line {$error['line']}";
        exit;
    }
}
register_shutdown_function('basicshutdownFunction');

if (!function_exists('json_validate')) {
function json_validate(string $json, int $depth = 512, int $flags = 0) {
   $tmp = json_decode($json);
   return json_last_error() === JSON_ERROR_NONE;
}
}

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

$logger = $container->get(LoggerInterface::class);

// Create App instance
$app = $container->get(App::class);

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$decoratedRequest = new DecoratedServerRequest($request);
$errorHandler = new HttpErrorHandler($app->getCallableResolver(), $app->getResponseFactory());
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $logger, $container, true);
register_shutdown_function($shutdownHandler);

// Register routes
(require __DIR__ . '/routes.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;