<?php

namespace PerSeo\MiddleWare;

class Maintenance
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
		if ($this->container->has('settings.global') && ($this->container->get('settings.global')['maintenance']) && ((!isset($_COOKIE['maintenance'])) || $_COOKIE['maintenance'] != $this->container->get('settings.global')['maintenancekey'])) {
			$lang = new \PerSeo\Translator($this->container->get('current.language'), \PerSeo\Path::LangPath('maintenance'));
            $langall = $lang->get();
            $this->container->set('view', function ($container) {
                $view = new \Slim\Views\Twig('modules/maintenance/views/' . $this->container->get('settings.global')['template'], [
                    'cache' => 'cache'
                ]);
                $router = $this->container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

                return $view;
            });
			return $this->container->get('view')->render($response, 'index.twig', [
                'host' => \PerSeo\Path::SiteName($request),
				'lang' => $langall['body'],
                'vars' => $this->container->get('Templater')->vars('maintenance')
            ]);
		}
        return $next($request, $response);
    }
}