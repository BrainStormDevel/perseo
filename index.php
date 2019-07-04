<?php

/*############################################
                 PerSeo CMS

        Copyright © 2019 BrainStorm
        https://www.per-seo.com

*/############################################

//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
try {
    @include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'version.php');
    if ((!@include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) || (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))) {
        throw new \Exception (__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php does not exist. Use composer to install dependencies.');
    }
    $app = new \PerSeo\NewApp;
    $container = $app->getContainer();
    $sanitize = new \PerSeo\Sanitizer($container);
    $app->add($sanitize);
    $container->set('Templater', function ($container) {
        $template = new \PerSeo\Template($container);
        return $template;
    });
	$app->add(new \PerSeo\WizardMiddleware($container));
    $LanguageMiddleware = function (\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next) use (
        $container
    ) {
        if ($container->has('settings.global') && ($container->get('settings.global')['locale'])) {
            $languages = $container->get('settings.global')['languages'];
            if (isset($_COOKIE['lang']) && in_array(strtolower($_COOKIE['lang']), $languages)) {
                $currlang = strtolower($_COOKIE['lang']);
            } else {
                if (in_array(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)), $languages)) {
                    $currlang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
                } else {
                    $currlang = $container->get('settings.global')['language'];
                }
            }
            $req = $request->getUri()->getPath();
            $basepath = $request->getUri()->getbasePath();
			$getpath = (empty($basepath) ? substr($request->getUri()->getPath(), 1) : $request->getUri()->getPath());
            $langurl = explode("/", $getpath);
            if (($request->isGet()) && ($req != '/') && ($langurl[0] != 'admin')) {
                if (!empty($langurl[0]) && (in_array($langurl[0], $languages))) {
                    $currlang = $langurl[0];
                    $container->set('redirect.url', $request->getUri()->getBasePath() . '/' . $currlang);
                    $finalstring = substr($getpath, strlen($currlang));
                    $request = $request->withUri($request->getUri()->withPath($finalstring));
                    $request = $request->withUri($request->getUri()->withbasePath($basepath));
                } else {
                    $container->set('current.language', $currlang);
                    throw new \Slim\Exception\NotFoundException($request, $response);
                }
            }
            if ($container->get('settings.global')['locale']) {
                $container->set('redirect.url', $request->getUri()->getBasePath() . '/' . $currlang);
            } else {
                $container->set('redirect.url', $request->getUri()->getBasePath());
            }
            $container->set('current.language', $currlang);
        } else {
            $container->set('redirect.url', $request->getUri()->getBasePath());
            if (isset($_COOKIE['lang'])) {
                $currlang = strtolower($_COOKIE['lang']);
            } else {
                if (!empty(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)))) {
                    $currlang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
                } else {
                    $currlang = 'en';
                }
            }
            $container->set('current.language', $currlang);
        }
        return $next($request, $response);
    };
    $app->add($LanguageMiddleware);
    $container->set('Sanitizer', function ($container) use ($sanitize) {
        return $sanitize;
    });
    if ($container->has('settings.secure')) {
        ini_set('session.save_handler', 'files');
        $handler = new \PerSeo\Sessions($container);
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
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if ($container->has('settings.database')) {
        $container->set('db', function ($container) {
			$instance = \PerSeo\DB::getInstance([
                'database_type' => $container->get('settings.database')['default']['driver'],
                'database_name' => $container->get('settings.database')['default']['database'],
                'server' => $container->get('settings.database')['default']['host'],
                'username' => $container->get('settings.database')['default']['username'],
                'password' => $container->get('settings.database')['default']['password'],
                'prefix' => $container->get('settings.database')['default']['prefix'],
                'charset' => $container->get('settings.database')['default']['charset']
            ]);
            return $instance->getConnection();
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
                $view = new \Slim\Views\Twig('modules/404/views/' . $container->get('settings.global')['template'], [
                    'cache' => 'cache'
                ]);
                $router = $container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

                return $view;
            });
            return $container->get('view')->render($response, '404.twig', [
                'host' => \PerSeo\Path::SiteName($request),
                'vars' => $container->get('Templater')->vars('404')
            ]);
        };
    });
    $app->add($container->get('csrf'));
    $directory = \PerSeo\Path::MOD_PATH;
    $dirobj = new \DirectoryIterator($directory);
    $modules = array();
    $curmod = 0;
    foreach ($dirobj as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $menu = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'menu.json';
            $routes = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'routes.php';
            $modules[$curmod]['name'] = $fileinfo->getBasename();
            if (file_exists($menu)) {
                $currfile = file_get_contents($menu);
                $modules[$curmod]['menu'] = json_decode($currfile, true);
            }
            if (file_exists($routes)) {
                @include_once($routes);
            }
            $curmod++;
        }
    }
    if (!empty($modules)) {
        $container->set('modules.name', $modules);
    }
    $app->run();
} catch (Exception $e) {
    die("PerSeo ERROR : " . $e->getMessage());
}