<?php

use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('modules', [
		'cache' => 'cache'
    ]);
	
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
	
    return $view;
};
$app->get('/login[/]', function (Request $request, Response $response, $args) use ($container) {
	$uri = $request->getUri()->getBasePath();
	return $response->withRedirect($uri. '/login/user', 307);
});
$app->get('/login/{name}[/]', function (Request $request, Response $response, $args) use ($container) {
	try {
		$csrfarray = array();
		$csrfarray['nameKey'] = $this->csrf->getTokenNameKey();
        $csrfarray['valueKey'] = $this->csrf->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
		\PerSeo\Path::$ModuleName = 'login';
		$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
		$lang->module('title');
		$lang->module('body');
		$faceapp = 'F_APP_'. $_SERVER['SERVER_NAME'];
		$facesecret = 'F_SECRET_'. $_SERVER['SERVER_NAME'];
		if (defined("$faceapp") && defined("$facesecret")) {
			$container['view']['faceapp'] = constant("$faceapp");
		}
		$googlekey = 'G_KEY_'. $_SERVER['SERVER_NAME'];
		$googlesecret = 'G_SECRET_'. $_SERVER['SERVER_NAME'];		
		if (defined("$googlekey") && defined("$googlesecret")) {
			$container['view']['googlekey'] = constant("$googlekey");
		}		
		return $this->view->render($response, '/login/views/index.tpl', [
			'titlesite' => constant("SITENAME"),
			'name' => $args['name'],
			'host' => \PerSeo\Path::SiteName($request),
			'csrf' => $csrfarray,
			'lang' => $lang->vars(),
			'vars' => \PerSeo\Template::vars()
		]);
	}
	catch(Exception $e) {
		die("PerSeo ERROR : " . $e->getMessage());
	}
})->setName('loginpage');
$app->post('/login/admin[/]', function (Request $request, Response $response, $args) use ($container) {
	\login\Controllers\Login::check($container);
});