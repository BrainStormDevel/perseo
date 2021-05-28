<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use PerSeo\DB;

class Alias
{
    protected $app;
    protected $container;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
        $this->db = ($container->has('db') ? $container->get('db') : null);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (!empty($this->db)) {
            $fulluri = (string) $request->getUri()->getPath();
            $basepath = (string) $this->app->getBasePath();
            $uri = (string) substr($fulluri, strlen($basepath));
            $filteredreq = preg_replace('/[^a-zA-Z0-9-_\-\/]/', '#', $uri);
            $regmatch = (((substr($filteredreq, -1) == '/') && (strlen($filteredreq) > 1)) ? substr_replace($filteredreq, '', -1)  : $filteredreq) . '([/]?)$';
            $result = $this->db->select('routes', [
            'request',
            'dest',
            'type',
            'redirect',
            'canonical',
            'priority' => DB::RAW('IF(REGEXP_REPLACE(request, \''. $regmatch .'\', 1) = 1, 1, 2)')
        ], [
            "OR" => [
                "AND #alias" => [
                    'request[REGEXP]' => $regmatch
                ],
                "AND #redirect" => [
                    'dest[REGEXP]' => $regmatch
                ]
            ],
            "ORDER" => 'priority'
        ]);
            if (!empty($result)) {
                $req = (string) $result[0]['request'];
                $dest = (string) $result[0]['dest'];
                $type = (string) $result[0]['type'];
                $redirect = (int) $result[0]['redirect'];
                $basepath = (string) $this->app->getBasePath();
                $mydest = (string) $basepath . $dest;
                if ($type == 1) {
                    if ($uri != $req) {
                        $mydest = (string) $basepath . $req;
                        $response = $this->app->getResponseFactory()->createResponse();
                        return $response->withHeader('Location', $mydest)->withStatus($redirect);
                    } else {
                        $request = $request->withUri($request->getUri()->withPath($mydest));
                    }
                } elseif ($type == 2) {
                    if ($uri != $dest) {
                        $response = $this->app->getResponseFactory()->createResponse();
                        return $response->withHeader('Location', $mydest)->withStatus($redirect);
                    }
                }
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}
