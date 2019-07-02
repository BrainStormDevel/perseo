<?php

namespace login\Controllers;

class CheckLogin extends Login
{

    public function __construct($container, $type)
    {
        $this->container = $container;
        $this->type = $type;
		$this->db = $container->get('db');
    }

    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
    {
        $uri = $this->container->get('redirect.url');
        if (!$this->islogged($this->type)) {
            $response = $response->withRedirect($uri . '/login/admin');
        } else {
            $response = $next($request, $response);
        }
        return $response;
    }
}