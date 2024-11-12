<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use BrainStorm\BasePath\BasePathMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Odan\Twig\TwigAssetsExtension;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\Middleware\SessionStartMiddleware;
use PerSeo\DB;
use PerSeo\LoggerFactory;
use PerSeo\MiddleWare\DefaultErrorRender;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

return [

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get('settings_logger'));
    },

    LoggerInterface::class => function (ContainerInterface $container): Logger {
        $loggerSettings = $container->get('settings_logger');
        
        $logger = new Logger($loggerSettings['name']);
        
        $processor = new UidProcessor();
        $logger->pushProcessor($processor);
        
        $handler = new StreamHandler($loggerSettings['path'] .'/'. $loggerSettings['filename'] . md5(date("mdy")) .'.log', $loggerSettings['level']);
        $logger->pushHandler($handler);
        
        return $logger;
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings_error');
        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
        $errorHandler = $errorMiddleware->getDefaultErrorHandler();
        $errorHandler->registerErrorRenderer('text/html', DefaultErrorRender::class);
        return $errorMiddleware;
    },
    
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },
    
    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings_session');
        $session = new PhpSession((array) $settings);
        return $session;
    }, 

    SessionStartMiddleware::class => function (ContainerInterface $container) {
        return new SessionStartMiddleware($container->get(SessionInterface::class));
    },
    
    'db' => function (ContainerInterface $container) {
        if ($container->has('settings_db')) {
            $settings = $container->get('settings_db');
            return new DB([
                    'database_type' => $settings['default']['driver'],
                    'database_name' => $settings['default']['database'],
                    'server' => $settings['default']['host'],
                    'username' => $settings['default']['username'],
                    'password' => $settings['default']['password'],
                    'prefix' => $settings['default']['prefix'],
                    'charset' => $settings['default']['charset']
            ]);
        }
    },
    
    Twig::class => function (ContainerInterface $container) {
        $twigSettings = $container->get('settings_twig');

        $options['debug'] = $twigSettings['debug'];
        $options['cache'] = $twigSettings['cache_enabled'] ? $twigSettings['cache_path'] : false;

        $twig = Twig::create($twigSettings['paths'], $options);

        $environment = $twig->getEnvironment();
        
        // Add extension here
        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new TwigAssetsExtension($environment, (array)$twigSettings));
 
        return $twig;
    },

    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class
        );
    },
];
