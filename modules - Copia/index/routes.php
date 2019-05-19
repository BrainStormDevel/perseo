<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

$container->set('view', function ($container) {
    $view = new \Slim\Views\Twig('modules', [
        'cache' => 'cache'
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
});
$app->get('/', function (Request $request, Response $response, $args) use ($container) {
    try {
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->csrf->getTokenNameKey();
        $csrfarray['valueKey'] = $this->csrf->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
        \PerSeo\Path::$ModuleName = 'index';
        $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
        $lang->module('title');
        $lang->module('body');
        $container['view']['csrf'] = $csrfarray;
        $container['view']['lang'] = $lang->vars();
        $container['view']['host'] = \PerSeo\Path::SiteName($request);
        $container['view']['vars'] = \PerSeo\Template::vars();
        $container['view']['cookiepath'] = \PerSeo\Path::cookiepath($request);
        return $this->view->render($response, '/index/views/index.tpl', [
            'name' => $args['params']
        ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
});