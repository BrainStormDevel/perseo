<?php

/*############################################
                 PerSeo CMS

        Copyright © 2019 BrainStorm
        https://www.per-seo.com

*/############################################

//error_reporting(E_ERROR | E_PARSE);
try {
    @include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'version.php');
    if ((!@include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) || (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))) {
        throw new \Exception (__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php does not exist. Use composer to install dependencies.');
    }
    $sanitize = new \PerSeo\Sanitizer();
    $app = new \PerSeo\NewApp;
    $app->add($sanitize);
    $container = $app->getContainer();
    $container->set('Sanitizer', function ($container) use ($sanitize) {
        return $sanitize;
    });
    if ($container->has('settings.secure')['crypt_salt']) {
        ini_set('session.save_handler', 'files');
        $key = $container->get('settings.secure')['crypt_salt'];
        $handler = new \PerSeo\Sessions($key);
        session_set_save_handler($handler, true);
    }
    if (ob_get_length()) {
        ob_end_clean();
    }
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))) {
        ob_start('ob_gzhandler');
    } else {
        ob_start();
    }
    session_start();
    if ($container->has('settings.database')['default']) {
        $container->set('db', function ($container) {
            return new \PerSeo\DB([
                'database_type' => $container->get('settings.database')['default']['driver'],
                'database_name' => $container->get('settings.database')['default']['database'],
                'server' => $container->get('settings.database')['default']['host'],
                'username' => $container->get('settings.database')['default']['username'],
                'password' => $container->get('settings.database')['default']['password'],
                'prefix' => $container->get('settings.database')['default']['prefix'],
                'charset' => $container->get('settings.database')['default']['charset']
            ]);
        });
    }
    $container->set('csrf', function () {
        $guard = new \Slim\Csrf\Guard();
        $guard->setPersistentTokenMode(true);
        return $guard;
    });
    $container->set('notFoundHandler', function ($container) {
        return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
            $container->set('view', function ($container) {
                $view = new \Slim\Views\Twig('modules', [
                    'cache' => 'cache'
                ]);
                $router = $container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

                return $view;
            });
            \PerSeo\Path::$ModuleName = '404';
            return $container->get('view')->render($response, '/404/views/404.tpl', [
                'host' => \PerSeo\Path::SiteName($request),
                'vars' => \PerSeo\Template::vars($container),
                'name' => $args['params']
            ]);
        };
    });
    $wizardMiddleware = function (\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next) use (
        $container
    ) {
        $route = $request->getAttribute('route');
        if (empty($route)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        $routeName = $route->getName();
        $publicRoutesArray = array(
            'wizard'
        );
        $uri = $request->getUri()->getBasePath();
        if (!$container->has('settings.database')['default'] && !in_array($routeName, $publicRoutesArray)) {
            $response = $response->withRedirect($uri . '/wizard');
        } else {
            $response = $next($request, $response);
        }
        return $response;
    };
    /*$adminMiddleware = function (\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next) use (
        $container
    ) {
        $route = $request->getAttribute('route');
        if (empty($route)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
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
    };*/
    $app->add($wizardMiddleware);
    //$app->add($adminMiddleware);
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
} catch (Exception $e) {
    die("PerSeo ERROR : " . $e->getMessage());
}