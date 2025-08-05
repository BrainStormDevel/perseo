<?php

use BrainStorm\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Middleware\ErrorMiddleware;
use PerSeo\Middleware\Language\Language;
use PerSeo\Middleware\Locale\Locale;
use PerSeo\MiddleWare\Alias;
use PerSeo\MiddleWare\Admin;
use PerSeo\MiddleWare\Maintenance;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use PerSeo\MiddleWare\Wizard;
use PerSeo\MiddleWare\HttpException\Html\HttpExceptionMiddleware;
use PerSeo\MiddleWare\ErrorHandlerMiddleware;
use PerSeo\Middleware\GZIP\GZIP;
use Odan\Session\Middleware\SessionStartMiddleware;

return function (App $app) {

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();
    
    $app->add(TwigMiddleware::class);

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();
    
    // Add locale in url Middleware
    $app->add(Locale::class);
    
    $app->add(Alias::class);
    
    $app->add(Maintenance::class);
    
    $app->add(Admin::class);
    
    $app->add(Wizard::class);
    
    // Set language from browser
    $app->add(Language::class);

    // Session
    $app->add(SessionStartMiddleware::class);
    
    //Add Basepath Middleware
    $app->add(BasePathMiddleware::class);
    
    $app->add(HttpExceptionMiddleware::class);
    
    $app->add(ErrorHandlerMiddleware::class);
    
    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
    
    //$app->add(GZIP::class);
};
