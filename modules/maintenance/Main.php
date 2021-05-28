<?php

namespace Modules\maintenance;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Main
{
	protected $app;
	protected $container;
    protected $twig;

    public function __construct(App $app, ContainerInterface $container, Twig $twig)
    {
		$this->app = $app;
		$this->container = $container;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response {
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
        ];
        return $this->twig->render($response, 'maintenance.twig', $viewData);
    }
}