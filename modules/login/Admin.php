<?php

namespace Modules\login;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Odan\Session\SessionInterface;

final class Admin
{
	protected $app;
	protected $db;
	protected $container;
    private $twig;

    public function __construct(App $app, ContainerInterface $container, Twig $twig, SessionInterface $session)
    {
		$this->app = $app;
		$this->container = $container;
		$this->session = $session;
        $this->twig = $twig;
		$this->db = ($container->has('db') ? $container->get('db') : null);
		$this->dbrev = ($container->has('dbrev') ? $container->get('dbrev') : null);
    }

    public function __invoke(Request $request, Response $response): Response {
		/*$result = $this->db->select('prova', [
                'id',
                'user',
                'pass',
                'superuser',
                'type'
            ], [
                'user' => $user
        ]);*/
		/*$result = $this->dbrev->select('AZ001DOC_MAST', [
                'mvserial'
            ]);*/
		if ($this->session->has('logged') && ($this->session->get('logged') === true)) {
			$finaluri = (string) $this->app->getBasePath() . '/';
			return $response->withHeader('Location', $finaluri)->withStatus(302);
		}
		else {
			$viewData = [
				'basepath' => (string) $this->app->getBasePath(),
				'user' => 1
			];
			return $this->twig->render($response, 'admin.twig', $viewData);
		}
    }
}