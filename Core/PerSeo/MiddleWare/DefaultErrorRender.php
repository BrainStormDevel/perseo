<?php

declare(strict_types=1);

namespace PerSeo\MiddleWare;

use Slim\Error\AbstractErrorRenderer;
use Throwable;
use Slim\App;
use Slim\Views\Twig;

class DefaultErrorRender extends AbstractErrorRenderer
{
	protected $app;
	protected $twig;
	
	public function __construct(App $app, Twig $twig)
    {
		$this->app = $app;
		$this->twig = $twig;
    }
	
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
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