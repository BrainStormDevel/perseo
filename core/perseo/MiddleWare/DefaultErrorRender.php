<?php

declare(strict_types=1);

namespace PerSeo\MiddleWare;

use Slim\Error\AbstractErrorRenderer;
use Throwable;
use Slim\App;
use Slim\Views\Twig;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DefaultErrorRender extends AbstractErrorRenderer
{
    protected $app;
    protected $twig;
    protected $logger;
    protected $settings;
    protected $template;
    
    public function __construct(App $app, LoggerInterface $logger, ContainerInterface $container, Twig $twig)
    {
        $this->app = $app;
        $this->logger = $logger;
        $this->twig = $twig;
        $this->settings = ($container->has('settings_global') ? $container->get('settings_global') : ['template' => 'default']);
        $this->template = $this->settings['template'];
    }
    
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $this->logger->error($exception);
        $viewData = [
            'debug' => $displayErrorDetails,
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'basepath' => (string) $this->app->getBasePath(),
            'template' => $this->template
        ];
        return (string) $this->twig->fetch($this->template . DIRECTORY_SEPARATOR .'500.twig', $viewData);
    }
}
