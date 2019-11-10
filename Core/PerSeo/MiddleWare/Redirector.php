<?php

namespace PerSeo\MiddleWare;

class Redirector
{
    private $container;
    private $request;
    private $response;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(
        \Slim\Http\Request $request,
        \Slim\Http\Response $response,
        callable $next
    ) {
        $this->request = $request;
        $this->response = $response;

        return $next($request, $response);
    }

    public function withBaseRedirect($uri, $type)
    {
        if ($this->container->has('settings.global') && ($this->container->get('settings.global')['locale'])) {
            $uriBase = '//'.$_SERVER['HTTP_HOST'].$this->request->getUri()->getBasePath().'/'.$this->container->get('current.language');
        } else {
            $uriBase = '//'.$_SERVER['HTTP_HOST'].$this->request->getUri()->getBasePath();
        }

        return $this->response->withRedirect($uriBase.$uri, $type);
    }
}
