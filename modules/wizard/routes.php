<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

$container->set('view', function ($container) {
    $view = new \Slim\Views\Twig('modules', [
        'cache' => false
        //'cache' => 'cache'
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
});
if (!\PerSeo\CheckConfig::verify()) {
    $app->any('/wizard[{params:\b(?!wizard\b).*\w+}]',
        function (Request $request, Response $response) use ($container) {
            if (!\PerSeo\CheckConfig::verify()) {
                return $response->withRedirect($request->getUri()->getBasePath());
            }
        });
} else {
    $app->get('/wizard[/]', function (Request $request, Response $response) use ($container) {
        try {
            $csrfarray = array();
            $csrfarray['nameKey'] = $this->get('csrf')->getTokenNameKey();
            $csrfarray['valueKey'] = $this->get('csrf')->getTokenValueKey();
            $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
            $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
            \PerSeo\Path::$ModuleName = 'wizard';
            $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
            $lang->module('title');
            $lang->module('body');
            return $this->get('view')->render($response, '/wizard/views/index.tpl', [
				'csrf' => $csrfarray,
				'lang' => $lang->vars(),
				'host' => \PerSeo\Path::SiteName($request),
				'vars' => \PerSeo\Template::vars(),
                'cookiepath' => \PerSeo\Path::cookiepath($request),
                'writeperm' => (is_writable(\PerSeo\Path::CONF_PATH) ? "ok" : "no"),
                'openssl' => (extension_loaded('openssl') ? "ok" : "no")
            ]);
        } catch (Exception $e) {
            die("PerSeo ERROR : " . $e->getMessage());
        }
    })->setName('wizard');
    $app->post('/wizard/test[/]', function (Request $request, Response $response) use ($container) {
        \wizard\Controllers\Test::main($container);
    })->setName('wizard');
    $app->post('/wizard/install[/]', function (Request $request, Response $response) use ($container) {
        \wizard\Controllers\Install::main($container);
    })->setName('wizard');
}