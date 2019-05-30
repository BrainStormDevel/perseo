<?php
$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
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
        \PerSeo\Path::$ModuleName = 'index';
        $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
        $lang->module('title');
        $lang->module('body');
        return $this->get('view')->render($response, '/index/views/index.twig', [
            'csrf' => $csrfarray,
            'lang' => $lang->vars(),
            'host' => \PerSeo\Path::SiteName($request),
            'vars' => \PerSeo\Template::vars($container)
        ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
});