<?php

namespace Modules\login\MiddleWare;

use Slim\App;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Odan\Session\SessionInterface;

final class CheckLogin
{
	protected $app;
	protected $session;

    public function __construct(App $app, SessionInterface $session)
    {
		$this->app = $app;
		$this->session = $session;
    }
	
    public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = $handler->handle($request);
		if (!$this->session->has('logged') || ($this->session->get('logged') != true)) {
			$finaluri = (string) $this->app->getBasePath() . '/admin';
			return $response->withHeader('Location', $finaluri)->withStatus(302);
		}
		return $response;
    }
}