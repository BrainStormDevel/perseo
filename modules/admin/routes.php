<?php
$app->get('/admin[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
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
            $panel->get('/admin/views/' . $container->get('settings.global')['template'] . '/admin/dashboard.twig'));
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container->has('settings.secure'), 'admins'));
$app->post('/admin/logout[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    //$mylogin = new \login\Controllers\Login($container, 'admins');
    //echo $mylogin->logout('admins');
})->add(new \login\Controllers\CheckLogin($container->has('settings.secure'), 'admins'));