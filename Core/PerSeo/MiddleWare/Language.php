<?php

namespace PerSeo\MiddleWare;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class Language implements Middleware
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookie = $request->getCookieParams();
        $server = $request->getServerParams();
        $settings = ($this->container->has('settings.global') ? $this->container->get('settings.global') : array());
        $languages = (!empty($settings['languages']) ? $settings['languages'] : array());
        if (isset($cookie['lang']) && in_array(strtolower($cookie['lang']), $languages)) {
            $currlang = strtolower($cookie['lang']);
        } else {
            if (in_array(strtolower(substr($server['HTTP_ACCEPT_LANGUAGE'], 0, 2)), $languages)) {
                $currlang = strtolower(substr($server['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            } else {
                if (isset($settings['language'])) {
                    $currlang = $settings['language'];
                } else {
                    $currlang = 'en';
                }
            }
        }
        $request = $request->withAttribute('language', $currlang);
        $response = $handler->handle($request);
        return $response;
    }
}
