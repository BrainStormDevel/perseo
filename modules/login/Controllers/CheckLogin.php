<?php

namespace login\Controllers;

class CheckLogin extends Login
{
	protected $redirect;
	
    public function __construct($container, $type)
    {
		$this->redirect = $container->get('Redirector');
		parent::__construct($container, $type);
    }

    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
    {
        if (!$this->islogged()) {
            $response = $this->redirect->withBaseRedirect('/login/admin', 307);
        } else {
            $response = $next($request, $response);
        }
        return $response;
    }
}