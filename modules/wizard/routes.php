<?php

if ($container->has('settings.database')['default']) {
    $app->any('/wizard[{params:\b(?!wizard\b).*\w+}]',
        function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {

            return $response->withRedirect($request->getUri()->getBasePath());

        });
} else {
    $app->get('/wizard[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
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
            \PerSeo\Path::$ModuleName = 'wizard';
            $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
            $lang->module('title');
            $lang->module('body');
            return $this->get('view')->render($response, '/wizard/views/index.twig', [
                'csrf' => $csrfarray,
                'lang' => $lang->vars(),
                'host' => \PerSeo\Path::SiteName($request),
                'vars' => \PerSeo\Template::vars($container),
                'cookiepath' => \PerSeo\Path::cookiepath($request),
                'writeperm' => (is_writable(\PerSeo\Path::CONF_PATH) ? "ok" : "no"),
                'openssl' => (extension_loaded('openssl') ? "ok" : "no")
            ]);
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->setName('wizard');
    $app->post('/wizard/test[/]',
        function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
            \wizard\Controllers\Test::main($container);
        })->setName('wizard');
    $app->post('/wizard/install[/]',
        function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
            \wizard\Controllers\Install::main($container);
        })->setName('wizard');
}