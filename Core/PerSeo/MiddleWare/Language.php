<?php

namespace PerSeo\MiddleWare;

use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Language
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
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
                $currlang = $settings['language'];
            }
        }
        $request = $request->withAttribute('language', $currlang);
        $response = $handler->handle($request);
        return $response;
    }
}
