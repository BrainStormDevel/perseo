<?php
$app->get('/login[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    $uri = $request->getUri()->getBasePath();
    return $response->withRedirect($uri . '/login/user', 307);
});
$app->get('/login/{name}[/]',
    function ($name, \Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
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
            \PerSeo\Path::$ModuleName = 'login';
            $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
            $lang->module('title');
            $lang->module('body');
            $faceapp = 'F_APP_' . $_SERVER['SERVER_NAME'];
            $facesecret = 'F_SECRET_' . $_SERVER['SERVER_NAME'];
            if (defined("$faceapp") && defined("$facesecret")) {
                $container['view']['faceapp'] = constant("$faceapp");
            }
            $googlekey = 'G_KEY_' . $_SERVER['SERVER_NAME'];
            $googlesecret = 'G_SECRET_' . $_SERVER['SERVER_NAME'];
            if (defined("$googlekey") && defined("$googlesecret")) {
                $container['view']['googlekey'] = constant("$googlekey");
            }
            return $this->get('view')->render($response, '/login/views/index.twig', [
                'titlesite' => $this->get('settings.global')['sitename'],
                'name' => $name,
                'host' => \PerSeo\Path::SiteName($request),
                'csrf' => $csrfarray,
                'lang' => $lang->vars(),
                'vars' => \PerSeo\Template::vars($container)
            ]);
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->setName('loginpage');
$app->post('/login/admin[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
	$login = new \login\Controllers\Login;
    $login->check($container);
});