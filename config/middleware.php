<?php

use BrainStorm\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Middleware\ErrorMiddleware;
use PerSeo\MiddleWare\Language;
use BrainStorm\Slim4Locale\Locale;
use PerSeo\MiddleWare\Alias;
use PerSeo\MiddleWare\Maintenance;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use PerSeo\MiddleWare\Wizard;
use PerSeo\MiddleWare\HttpExceptionMiddleware;
use PerSeo\MiddleWare\ErrorHandlerMiddleware;
use Odan\Session\Middleware\SessionMiddleware;

return function (App $app) {
    $settings = $app->getContainer()->get('settings.global');
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();
    
    $app->add(TwigMiddleware::class);

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();
    
    // Add locale in url Middleware
    $app->add(new Locale($app, $settings['locale'], $settings['languages']));
    
    $app->add(Alias::class);
    
    $app->add(Maintenance::class);
    
    $app->add(Wizard::class);
    
    // Set language from browser
    $app->add(Language::class);

    // Session
    $app->add(SessionMiddleware::class);
    
    //Add Basepath Middleware
    $app->add(BasePathMiddleware::class);
    
    $app->add(HttpExceptionMiddleware::class);
    
    $app->add(ErrorHandlerMiddleware::class);
    
    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
};
