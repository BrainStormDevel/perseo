<?php
$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    try {
        $container->set('view', function ($container) {
            $view = new \Slim\Views\Twig('modules/index/views/' . $container->get('settings.global')['template'], [
                'cache' => false
            ]);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
            return $view;
        });
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->get('csrf')->getTokenNameKey();
        $csrfarray['valueKey'] = $this->get('csrf')->getTokenValueKey();
        $csrfarray['name'] = $request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $request->getAttribute($csrfarray['valueKey']);
        $lang = new \PerSeo\Translator($container->get('current.language'), \PerSeo\Path::LangPath('index'));
        $langall = $lang->get();
        return $this->get('view')->render($response, 'index.twig', [
            'csrf' => $csrfarray,
            'lang' => $langall['body'],
            'host' => \PerSeo\Path::SiteName($request),
            'vars' => $container->get('Templater')->vars('index')
        ]);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
});