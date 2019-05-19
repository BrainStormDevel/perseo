<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

$container->set('view', function ($container) {
    $view = new \Slim\Views\Twig('modules', [
        'cache' => false
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
});
$app->get('/users/admin[/]', function (Request $request, Response $response) use ($container) {
    try {
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->get('csrf')->getTokenNameKey();
        $csrfarray['valueKey'] = $this->get('csrf')->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
        \PerSeo\Path::$ModuleName = 'users';
        $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
        $lang->module('title');
        $lang->module('body');
        return $this->get('view')->render($response, '/users/views/admin/index.tpl', [
            'titlesite' => constant("SITENAME"),
			'username' => \PerSeo\Login::username(),
			'menuarray' => \admin\Controllers\Menu::listall(),
			'bodytpl' => '/users/views/admin/dashboard.tpl',
            'host' => \PerSeo\Path::SiteName($request),
			'adm_host' => \PerSeo\Path::SiteName($request) . '/admin',
            'csrf' => $csrfarray,
            'lang' => $lang->vars(),
            'vars' => \PerSeo\Template::vars(),
			'cookiepath' => \PerSeo\Path::cookiepath($request)
        ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->setName('requireadmin');
$app->post('/users/logout[/]', function (Request $request, Response $response) use ($container) {
    $mylogin = new \PerSeo\Login();
    echo $mylogin->logout('admins');
})->setName('requireadmin');