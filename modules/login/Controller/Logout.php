<?php

namespace Modules\login\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Odan\Session\SessionInterface;

final class Logout
{
	protected $session;

    public function __construct(SessionInterface $session)
    {
		$this->session = $session;
    }
	
    public function __invoke(Request $request, Response $response): Response {
		$this->session->destroy();
		$result = array(
			'code' => '0',
			'msg' => 'OK'
		);
		$response->getBody()->write(json_encode($result));
		return $response;
    }
}