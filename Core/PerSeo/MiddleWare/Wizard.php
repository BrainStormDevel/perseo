<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use PerSeo\DB;

class Wizard
{
    protected $app;
    protected $container;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $fulluri = (string) $request->getUri()->getPath();
        $basepath = (string) $this->app->getBasePath();
        $uri = (string) substr($fulluri, strlen($basepath));
        preg_match("/(^\/(wizard\/)|\/(wizard\b))/i", $uri, $matches);
        if (!$this->container->has('settings.db')) {
            if (empty($matches[0])) {
                if ($request->getMethod() == 'GET') {
                    $mydest = $basepath .'/wizard/';
                    $response = $this->app->getResponseFactory()->createResponse();
                    return $response->withHeader('Location', $mydest)->withStatus(301);
                } elseif ($request->getMethod() == 'POST') {
                    throw new \Slim\Exception\HttpNotFoundException($request);
                }
            }
        } else {
            if (!empty($matches[0])) {
                throw new \Slim\Exception\HttpNotFoundException($request);
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}
