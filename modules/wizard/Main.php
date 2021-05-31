<?php

namespace Modules\wizard;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PerSeo\Translator;

final class Main
{
    protected $app;
    protected $db;
    protected $container;
    protected $session;
    private $twig;

    public function __construct(App $app, ContainerInterface $container, Twig $twig)
    {
	$this->app = $app;
	$this->container = $container;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response {
	$config = $this->container->get('settings.root') .'/config';
	$module = $this->container->get('settings.modules') .'/wizard';
	$lang = new Translator($request->getAttribute('language'), $module);
	$langs = $lang->get();
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
			'cookiepath' => (string) (!empty($this->app->getBasePath()) ? $this->app->getBasePath() : '') .'/',
            'writeperm' => (is_writable($config) ? "ok" : "no"),
			'language' => $request->getAttribute('language'),
            'openssl' => (extension_loaded('openssl') ? "ok" : "no"),
			'lang' => $langs['body']
        ];		
		return $this->twig->render($response, 'wizard.twig', $viewData);
    }
}
