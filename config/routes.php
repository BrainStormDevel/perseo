<?php

use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

//$settings = require \PerSeo\Path::CONF_PATH . \PerSeo\Path::DS . 'settings.php';
$sanitize = new \PerSeo\Sanitizer();
$app = new \PerSeo\NewApp;
//var_dump($app);
$app->add($sanitize);
$container = $app->getContainer();
$container->set('Sanitizer', function($container) use ($sanitize) {
    return $sanitize;
});
//$app->add(new Slim\Csrf\Guard());
/*$container['Sanitizer'] = function ($container) use ($sanitize) {
    return $sanitize;
};*/
/*$container['csrf'] = function ($container) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setPersistentTokenMode(true);
    return $guard;
};*/
$container->set('csrf', function() {
    $guard = new \Slim\Csrf\Guard();
    $guard->setPersistentTokenMode(true);
    return $guard;
});
$container->set('notFoundHandler', function ($container) {
    return function (Request $request, Response $response) use ($container) {
        $container->set('view', function ($container) {
            $view = new \Slim\Views\Twig('modules', [
                'cache' => 'cache'
            ]);
            $router = $container->get('router');
            $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

            return $view;
        });
        \PerSeo\Path::$ModuleName = '404';
        //$container['view']['host'] = \PerSeo\Path::SiteName($request);
        //$container['view']['vars'] = \PerSeo\Template::vars();
        //$container['view']['cookiepath'] = \PerSeo\Path::cookiepath($request);
        return $container->get('view')->render($response, '/404/views/404.tpl', [
            'name' => $args['params']
        ]);
    };
});
$wizardMiddleware = function (Request $request, Response $response, callable $next) use ($container) {
    $route = $request->getAttribute('route');
    if (empty($route)) {
        throw new NotFoundException($request, $response);
    }
    $routeName = $route->getName();
    $publicRoutesArray = array(
        'wizard'
    );
    $uri = $request->getUri()->getBasePath();
    if (\PerSeo\CheckConfig::verify() && !in_array($routeName, $publicRoutesArray)) {
        $response = $response->withRedirect($uri . '/wizard');
    } else {
        $response = $next($request, $response);
    }
    return $response;
};
$adminMiddleware = function (Request $request, Response $response, callable $next) use ($container) {
    $route = $request->getAttribute('route');
    if (empty($route)) {
        throw new NotFoundException($request, $response);
    }
    $routeName = $route->getName();
    $publicRoutesArray = array(
        'requireadmin'
    );
    $login = new \PerSeo\Login;
    $uri = $request->getUri()->getBasePath();
    if (!$login->islogged('admins') && in_array($routeName, $publicRoutesArray)) {
        $response = $response->withRedirect($uri . '/login/admin');
    } else {
        $response = $next($request, $response);
    }
    return $response;
};
$app->add($wizardMiddleware);
$app->add($adminMiddleware);
$app->add($container->get('csrf'));
$directory = \PerSeo\Path::MOD_PATH;
$dirobj = new \DirectoryIterator($directory);
foreach ($dirobj as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $routes = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'routes.php';
        if (file_exists($routes)) {
            @include_once($routes);
        }
    }
}
$app->run();