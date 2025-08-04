<?php

namespace Modules\index\Views;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

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
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
			'template' => $this->template
        ];
        return $this->twig->render($response, $this->template . DIRECTORY_SEPARATOR .'index.twig', $viewData);
    }
}