<?php

declare(strict_types=1);

namespace PerSeo\MiddleWare;

use Slim\Error\AbstractErrorRenderer;
use Throwable;
use Slim\App;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

class DefaultErrorRender extends AbstractErrorRenderer
{
    protected $app;
    protected $twig;
    protected $logger;
    
    public function __construct(App $app, LoggerInterface $logger, Twig $twig)
    {
        $this->app = $app;
        $this->logger = $logger;
        $this->twig = $twig;
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
            'basepath' => (string) $this->app->getBasePath()
        ];
        return (string) $this->twig->fetch('500.twig', $viewData);
    }
}
