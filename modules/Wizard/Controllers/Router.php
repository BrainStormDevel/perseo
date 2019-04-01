<?php

namespace Wizard\Controllers;

class Router
{
	public function init($request, $response, $args, $app, $csrf = NULL)
    {
		//$container = $app->getContainer();
		//var_dump($container);
		if (empty($args) || empty($args['params'])) { \Wizard\Controllers\Start::main($request, $response, $args, $app);  }
		else { echo "Start<br>"; }
		//print_r($args);
		//return $response->write("<html><head></head><body>questa è una prova</body></html>");
	}
}