<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Maintenance
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
        $cookies = $request->getCookieParams();
        if ($this->container->has('settings.global')) {
            $settings = $this->container->get('settings.global');
            if ($settings['maintenance']) {
                if (!isset($cookies['maintenance']) || ($cookies['maintenance'] != $settings['maintenancekey'])) {
                    $fulluri = (string) $request->getUri()->getPath();
                    $basepath = (string) $this->app->getBasePath();
                    $uri = (string) substr($fulluri, strlen($basepath));
                    $language = $request->getAttribute('language');
                    $locale = ($settings['locale'] ? '/'. $language : '');
                    $mydest = (string) $basepath . $locale .'/maintenance';
                    if ($uri != $locale .'/maintenance') {
                        $response = $this->app->getResponseFactory()->createResponse();
                        return $response->withHeader('Location', $mydest)->withStatus(301);
                    }
                }
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}
