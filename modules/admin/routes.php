<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('modules', [
        'cache' => false
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
};
$app->get('/admin[/]', function (Request $request, Response $response, $args) use ($container) {
    try {
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->csrf->getTokenNameKey();
        $csrfarray['valueKey'] = $this->csrf->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
        \PerSeo\Path::$ModuleName = 'admin';
        $lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangAdminPath());
        $lang->module('title');
        $lang->module('body');
        $container['view']['csrf'] = $csrfarray;
        $container['view']['lang'] = $lang->vars();
        $container['view']['titlesite'] = constant("SITENAME");
        $container['view']['username'] = \PerSeo\Login::username();
        $container['view']['bodytpl'] = '/admin/views/admin/dashboard.tpl';
        $container['view']['menuarray'] = \admin\Controllers\Menu::listall();
        $container['view']['host'] = \PerSeo\Path::SiteName($request);
        $container['view']['adm_host'] = \PerSeo\Path::SiteName($request) . '/admin';
        $container['view']['vars'] = \PerSeo\Template::vars();
        $container['view']['cookiepath'] = \PerSeo\Path::cookiepath($request);
        return $this->view->render($response, '/admin/views/admin/index.tpl', [
            'name' => $args['params']
        ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->setName('requireadmin');
$app->post('/admin/logout[/]', function (Request $request, Response $response, $args) use ($container) {
    $mylogin = new \PerSeo\Login();
    echo $mylogin->logout('admins');
})->setName('requireadmin');