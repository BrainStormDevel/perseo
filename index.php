<?php

/*############################################
                 PerSeo CMS

        Copyright © 2019 BrainStorm
        https://www.per-seo.com

*/############################################

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
try {
    @include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'version.php');
    if ((!@include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) || (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))) {
        throw new \Exception (__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php does not exist. Use composer to install dependencies.');
    }
    $app = new \PerSeo\NewApp;
    $container = $app->getContainer();
    if ($container->has('settings.database')) {
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
    $sanitize = new \PerSeo\MiddleWare\Sanitizer($container);
    $redirector = new \PerSeo\MiddleWare\Redirector($container);
    $container->set('Templater', function ($container) {
        $template = new \PerSeo\Template($container);
        return $template;
    });
    $container->set('Sanitizer', function ($container) use ($sanitize) {
        return $sanitize;
    });
    $container->set('Redirector', function ($container) use ($redirector) {
        return $redirector;
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
    $container->set('csrf', function () {
        $guard = new \Slim\Csrf\Guard();
        $guard->setPersistentTokenMode(true);
        return $guard;
    });
    $container->set('notFoundHandler', function ($container) {
        return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
            $lang = new \PerSeo\Translator($container->get('current.language'), \PerSeo\Path::LangPath('404'));
            $langall = $lang->get();
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
                'lang' => $langall['body'],
                'vars' => $container->get('Templater')->vars('404')
            ]);
        };
    });
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
    $app->add($sanitize);
    $app->add($redirector);
	$app->add(new \PerSeo\MiddleWare\Maintenance($container));
	$app->add(new \PerSeo\MiddleWare\Language($container));
	$app->add(new \PerSeo\MiddleWare\Wizard($container));
	$app->add($container->get('csrf'));
    $app->run();
} catch (Exception $e) {
    die("PerSeo ERROR : " . $e->getMessage());
}