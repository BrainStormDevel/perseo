<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Slim\Psr7\Stream as Stream;

class GZIP implements Middleware
{
    protected $app;
    protected $container;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if ($request->hasHeader('Accept-Encoding') && stristr($request->getHeaderLine('Accept-Encoding'), 'gzip') === false) {
            // Browser doesn't accept gzip compression
            return $handler->handle($request);
        }

        /** @var Response $response */
        $response = $handler->handle($request);

        if ($response->hasHeader('Content-Encoding')) {
            return $handler->handle($request);
        }

        // Compress response data
        $deflateContext = deflate_init(ZLIB_ENCODING_GZIP);
        $compressed = deflate_add($deflateContext, (string)$response->getBody(), \ZLIB_FINISH);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $compressed);
        rewind($stream);

        return $response
        ->withHeader('Content-Encoding', 'gzip')
        ->withHeader('Content-Length', strlen($compressed))
        ->withBody(new Stream($stream));
    }
}
