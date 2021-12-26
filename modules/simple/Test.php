<?php

namespace Modules\simple;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Test
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
		//$response->getBody()->write('Hello World');
		//return $response;
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
        ];
        return $this->twig->render($response, 'test.twig', $viewData);
    }
}