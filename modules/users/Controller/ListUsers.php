<?php

namespace Modules\users\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Odan\Session\SessionInterface;

final class ListUsers
{
    protected $db;
	protected $session;

    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
		$this->session = $session;
        $this->db = ($container->has('db') ? $container->get('db') : null);
    }
	
    public function __invoke(Request $request, Response $response): Response {
		$users = $this->db->select('users', [
                'id',
				'nome',
				'cognome',
                'user',
                'ruolo'
        ]);
		$result = json_encode($users);
		$response->getBody()->write($result);
		return $response;
    }
}