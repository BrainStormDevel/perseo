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
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->get('csrf')->getTokenNameKey();
        $csrfarray['valueKey'] = $this->get('csrf')->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
        \PerSeo\Path::$ModuleName = 'admin';
        $lang = new \PerSeo\Translator($container->get('current.language'), \PerSeo\Path::LangAdminPath());
        return $this->get('view')->render($response,
            '/admin/views/' . $container->get('settings.global')['template'] . '/admin/index.twig', [
                'csrf' => $csrfarray,
                'lang' => $lang->get(),
                'titlesite' => $this->get('settings.global')['sitename'],
                'username' => \login\Controllers\Login::username(),
                'bodytpl' => '/admin/views/' . $container->get('settings.global')['template'] . '/admin/dashboard.twig',
                'menuarray' => \admin\Controllers\Menu::listall(),
                'host' => \PerSeo\Path::SiteName($request),
                'adm_host' => \PerSeo\Path::SiteName($request) . '/admin',
                'vars' => \PerSeo\Template::vars($container),
                'cookiepath' => \PerSeo\Path::cookiepath($request)
            ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container, 'admins'));
$app->post('/admin/logout[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    $mylogin = new \login\Controllers\Login;
    echo $mylogin->logout($container, 'admins');
})->add(new \login\Controllers\CheckLogin($container, 'admins'));