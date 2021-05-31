<?php

namespace PerSeo\MiddleWare;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class Language implements Middleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookie = $request->getCookieParams();
        $settings = ($this->container->has('settings.global') ? $this->container->get('settings.global') : array());
        $languages = $settings['languages'];
        if (isset($cookie['lang']) && in_array(strtolower($cookie['lang']), $languages)) {
            $currlang = strtolower($cookie['lang']);
        } else {
            if (in_array(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)), $languages)) {
                $currlang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
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
