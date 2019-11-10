<?php

namespace PerSeo\MiddleWare;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ErrorHandler
{
    private $container;
    private $request;
    private $response;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(
        Request $request,
        Response $response,
        \Throwable $e
    ) {
        switch (true) {
            case ($e->getCode() >= 100) && ($e->getCode() < 200):
            $output = [
                'uri'    => $request->getUri()->getPath(),
                'code'   => $e->getCode(),
                'message'=> $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ];
            $silent = [
                'code'   => $e->getCode(),
                'message'=> $e->getMessage(),
            ];
            if ($this->container->has('loggerWarning')) {
                $monoLog = $this->container->get('loggerWarning');
                $monoLog->warning(json_encode($output));
            }
            if ($this->container->has('myresponse')) {
                $myresponse = $this->container->get('myresponse');
                switch ($myresponse['type']) {
                    case 'json':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->withJson($output);
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->withJson($silent);
                    }
                    break;
                    case 'text/html':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->write(json_encode($output));
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->write(json_encode($silent));
                    }
                    break;
                    default:
                    $response->getBody()->rewind();

                    return $response->withStatus(200)
                        ->withHeader('Content-Type', 'text/html')
                        ->write(json_encode($silent));
                }
            } else {
                $response->getBody()->rewind();

                return $response->withStatus(200)
                ->withHeader('Content-Type', 'text/html')
                ->write(json_encode($silent));
            }
            break;
            case ($e->getCode() >= 200) && ($e->getCode() < 300):
            $output = [
                'uri'    => $request->getUri()->getPath(),
                'code'   => $e->getCode(),
                'message'=> $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ];
            $silent = [
                'code'   => $e->getCode(),
                'message'=> $e->getMessage(),
            ];
            if ($this->container->has('myresponse')) {
                $myresponse = $this->container->get('myresponse');
                switch ($myresponse['type']) {
                    case 'json':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->withJson($output);
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->withJson($silent);
                    }
                    break;
                    case 'text/html':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->write(json_encode($output));
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(200)->write(json_encode($silent));
                    }
                    break;
                    default:
                    $response->getBody()->rewind();

                    return $response->withStatus(200)
                        ->withHeader('Content-Type', 'text/html')
                        ->write(json_encode($silent));
                }
            } else {
                $response->getBody()->rewind();

                return $response->withStatus(200)
                ->withHeader('Content-Type', 'text/html')
                ->write(json_encode($silent));
            }
            break;
            default:
            $ecode = ($e->getCode() == 0 ? 1 : $e->getCode());
            $output = [
                'uri'    => $request->getUri()->getPath(),
                'code'   => $ecode,
                'message'=> $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ];
            $silent = [
                'code'   => $ecode,
                'message'=> $e->getMessage(),
            ];
            if ($this->container->has('loggerCritical')) {
                $monoLog = $this->container->get('loggerCritical');
                $monoLog->critical(json_encode($output));
            }
            if ($this->container->has('myresponse')) {
                $myresponse = $this->container->get('myresponse');
                switch ($myresponse['type']) {
                    case 'json':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(500)->withJson($output);
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(500)->withJson($silent);
                    }
                    break;
                    case 'text/html':
                    if ($myresponse['verbose']) {
                        $response->getBody()->rewind();

                        return $response->withStatus(500)->write(json_encode($output));
                    } else {
                        if (!empty($myresponse['message'])) {
                            $silent['message'] = $myresponse['message'];
                        }
                        $response->getBody()->rewind();

                        return $response->withStatus(500)->write(json_encode($silent));
                    }
                    break;
                    default:
                    $response->getBody()->rewind();

                    return $response->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->write(json_encode($silent));
                }
            } else {
                $response->getBody()->rewind();

                return $response->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write(json_encode($silent));
            }
        }
    }
}
