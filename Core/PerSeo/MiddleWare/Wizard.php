<?php

namespace PerSeo\MiddleWare;

class Wizard
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
    {
        if (!$this->container->has('settings.global')) {
            $route = $request->getAttribute('route');
            $uri = $request->getUri()->getBasePath();
            if (empty($route)) {
                return $response->withRedirect($uri . '/wizard');
            }
            $routeName = $route->getName();
            $publicRoutesArray = array(
                'wizard'
            );
            if (!$this->container->has('settings.database') && !in_array($routeName, $publicRoutesArray)) {
                return $response->withRedirect($uri . '/wizard');
            }
        }
        return $next($request, $response);
    }
}