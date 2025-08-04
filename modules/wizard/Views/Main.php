<?php

namespace Modules\wizard\Views;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PerSeo\Translator;

final class Main
{
    private $app;
    private $container;
    private $twig;
    private $settings;
    private $template;

    public function __construct(App $app, ContainerInterface $container, Twig $twig)
    {
		$this->app = $app;
		$this->container = $container;
        $this->twig = $twig;
		$this->settings = ($container->has('settings_global') ? $container->get('settings_global') : ['template' => 'default']);
        $this->template = $this->settings['template'];
    }

    public function __invoke(Request $request, Response $response): Response {
	$config = $this->container->get('settings_root') .'/config';
	$module = $this->container->get('settings_modules') .'/wizard';
	$lang = new Translator($request->getAttribute('language'), $module);
	$langs = $lang->get();
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
			'cookiepath' => (string) (!empty($this->app->getBasePath()) ? $this->app->getBasePath() : '') .'/',
            'writeperm' => (is_writable($config) ? "ok" : "no"),
			'language' => $request->getAttribute('language'),
            'openssl' => (extension_loaded('openssl') ? "ok" : "no"),
			'lang' => $langs['body'],
			'template' => $this->template
        ];		
		return $this->twig->render($response, $this->template . DIRECTORY_SEPARATOR .'wizard.twig', $viewData);
    }
}
