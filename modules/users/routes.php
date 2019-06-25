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
				'/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig', $panel->get('/users/views/' . $container->get('settings.global')['template'] . '/admin/gestall.twig', 'users'));
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container, 'admins'));

$app->get('/admin/users/edit_admins[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
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
				'/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig', $panel->get('/users/views/' . $container->get('settings.global')['template'] . '/admin/gestadmins.twig', 'users'));
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container, 'admins'));