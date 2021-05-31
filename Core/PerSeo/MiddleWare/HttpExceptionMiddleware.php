<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;
use Slim\Views\Twig;

final class HttpExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $app;
    private $responseFactory;
    protected $twig;

    public function __construct(App $app, ResponseFactoryInterface $responseFactory, Twig $twig)
    {
        $this->app = $app;
        $this->responseFactory = $responseFactory;
        $this->twig = $twig;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $httpException) {
            $statusCode = $httpException->getCode();
            $response = $this->responseFactory->createResponse()->withStatus($statusCode);
            $errorMessage = sprintf('%s %s', $statusCode, $response->getReasonPhrase());
            $viewData = [
                'basepath' => (string) $this->app->getBasePath()
            ];
            return $this->twig->render($response, '404.twig', $viewData);
        }
    }
}
