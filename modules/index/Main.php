<?php

namespace Modules\index;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Odan\Session\SessionInterface;

final class Main
{
	protected $app;
	protected $db;
	protected $container;
	protected $session;
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
		$type = (string) $this->session->get('type');
		$bodytpl = (($type == 'admins') ? 'mainadmin.twig' : 'mainuser.twig');
        $viewData = [
			'basepath' => (string) $this->app->getBasePath(),
			'uid' => (int) $this->session->get('uid'),
			'type' => (string) $this->session->get('type'),
            'user' => (string) $this->session->get('user'),
			'nome' => (string) $this->session->get('nome'),
			'cognome' => (string) $this->session->get('cognome'),
			'ruolo' => (int) $this->session->get('ruolo'),
			'bodytpl' => $bodytpl
        ];
        return $this->twig->render($response, 'index.twig', $viewData);
    }
}