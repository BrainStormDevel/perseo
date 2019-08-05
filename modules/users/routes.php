<?php

$app->get('/admin/users[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    try {
        $container->set('view', function ($container) {
            $view = new \Slim\Views\Twig('modules', [
                'cache' => false
            ]);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
            return $view;
        });
        $panel = new \admin\Controllers\Panel($container, $request);
        return $this->get('view')->render($response,
            '/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig',
            $panel->get('/users/views/' . $container->get('settings.global')['template'] . '/admin/gestall.twig',
                'users'));
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container, 'admins'));

$app->get('/admin/users/edit_admins[/]',
    function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
        try {
            $container->set('view', function ($container) {
                $view = new \Slim\Views\Twig('modules', [
                    'cache' => false
                ]);
                $router = $container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
                return $view;
            });
            $panel = new \admin\Controllers\Panel($container, $request);
            $users = new \users\Controllers\Listusers($container);
            $panel->add('users', $users->admins());
            $panel->add('adminstypes', $users->adminstype());
            $panel->add('langdatatable', \users\Controllers\Locale::get($container->get('current.language')));
            return $this->get('view')->render($response,
                '/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig',
                $panel->get('/users/views/' . $container->get('settings.global')['template'] . '/admin/gestadmins.twig',
                    'users',
                    '/users/views/' . $container->get('settings.global')['template'] . '/admin/head_include.twig',
                    '/users/views/' . $container->get('settings.global')['template'] . '/admin/foot_include.twig'));
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->add(new \login\Controllers\CheckLogin($container, 'admins'));
$app->post('/admin/users/edit_admins[/]',
    function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
        try {
            $edit = new \users\Controllers\GestAdmin($container);
            echo json_encode(($edit->AddNew()));
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->add(new \login\Controllers\CheckLogin($container, 'admins'));
$app->post('/admin/users/del_admin[/]',
    function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
        try {
            $edit = new \users\Controllers\GestAdmin($container);
            echo json_encode(($edit->Del()));
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->add(new \login\Controllers\CheckLogin($container, 'admins'));
$app->get('/admin/users/edit_users[/]',
    function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
        try {
            $container->set('view', function ($container) {
                $view = new \Slim\Views\Twig('modules', [
                    'cache' => false
                ]);
                $router = $container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
                return $view;
            });
            $panel = new \admin\Controllers\Panel($container, $request);
            $users = new \users\Controllers\Listusers($container);
            $panel->add('users', $users->users());
            $panel->add('adminstypes', $users->adminstype());
            $panel->add('langdatatable', \users\Controllers\Locale::get($container->get('current.language')));
            return $this->get('view')->render($response,
                '/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig',
                $panel->get('/users/views/' . $container->get('settings.global')['template'] . '/admin/gestusers.twig',
                    'users',
                    '/users/views/' . $container->get('settings.global')['template'] . '/admin/head_include.twig',
                    '/users/views/' . $container->get('settings.global')['template'] . '/admin/foot_include.twig'));
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->add(new \login\Controllers\CheckLogin($container, 'admins'));