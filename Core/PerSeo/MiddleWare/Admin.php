<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use PerSeo\DB;

class Admin implements Middleware
{
    protected $app;
    protected $container;
    protected $settings;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
        $this->settings = $container->get('settings.global');
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!empty($this->settings['adminpath'])) {
            $adminpath = '/'. $this->settings['adminpath'];
            $adminpath2 = $adminpath .'/';
            $fulluri = (string) $request->getUri()->getPath();
            $basepath = (string) $this->app->getBasePath();
            $uri = (string) substr($fulluri, strlen($basepath));
            if (($uri == '/admin') || (substr($uri, 0, 7) == '/admin/')) {
                throw new \Slim\Exception\HttpNotFoundException($request);
            }
            if (($adminpath == $uri) || (substr($uri, 0, strlen($adminpath) + 1) == $adminpath2)) {
                $basepath = (string) $this->app->getBasePath();
                $mydest = (string) $basepath . str_replace($adminpath, '/admin', $uri);
                $request = $request->withUri($request->getUri()->withPath($mydest));
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}
