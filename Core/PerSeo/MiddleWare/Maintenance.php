<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class Maintenance implements Middleware
{
    protected $app;
    protected $container;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookies = $request->getCookieParams();
        if ($this->container->has('settings.global')) {
            $settings = $this->container->get('settings.global');
            $fulluri = (string) $request->getUri()->getPath();
            $basepath = (string) $this->app->getBasePath();
            $uri = (string) substr($fulluri, strlen($basepath));
            $language = $request->getAttribute('language');
            $locale = (!empty($settings['locale']) ? '/'. $language : '');
            if (!empty($settings['maintenance'])) {
                if (!isset($cookies['maintenance']) || ($cookies['maintenance'] != $settings['maintenancekey'])) {
                    $mydest = (string) $basepath . $locale .'/maintenance';
                    if ($uri != $locale .'/maintenance') {
                        $response = $this->app->getResponseFactory()->createResponse();
                        return $response->withHeader('Location', $mydest)->withStatus(301);
                    }
                }
            } else {
                if ($uri == $locale .'/maintenance') {
                    $mydest = (string) $basepath .'/';
                    $response = $this->app->getResponseFactory()->createResponse();
                    return $response->withHeader('Location', $mydest)->withStatus(301);
                }
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}
