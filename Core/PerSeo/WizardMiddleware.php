<?php

namespace PerSeo;

class WizardMiddleware {
	
    private $container;

    public function __construct($container) {
        $this->container = $container;
    }
	
	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next) {
		if (!$this->container->has('settings.global')) {
			$route = $request->getAttribute('route');
			if (empty($route)) {
				throw new \Slim\Exception\NotFoundException($request, $response);
			}
			$routeName = $route->getName();
			$publicRoutesArray = array(
				'wizard'
			);
			$uri = $request->getUri()->getBasePath();
			if (!$this->container->has('settings.database') && !in_array($routeName, $publicRoutesArray)) {
				return $response->withRedirect($uri . '/wizard');
			}
		}
        return $next($request, $response);
	}
}