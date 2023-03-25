<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use PerSeo\DB\DBDefault;

class Alias implements Middleware
{
    protected $app;
    protected $db;

    public function __construct(App $app, DBDefault $db)
    {
        $this->app = $app;
        $this->db = $db;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!empty($this->db) && is_object($this->db)) {
            $fulluri = (string) $request->getUri()->getPath();
            $basepath = (string) $this->app->getBasePath();
            $uri = (string) substr($fulluri, strlen($basepath));
            $filteredreq = preg_replace('/[^a-zA-Z0-9-_.\-\/]/', '#', $uri);
			$notonlynumeric = preg_replace('/[^a-zA-Z0-9]/', '', $filteredreq);
            $regmatch = (((substr($filteredreq, -1) == '/') && (strlen($filteredreq) > 1)) ? substr_replace($filteredreq, '', -1)  : $filteredreq) . '([/]?)$';
			if (!is_numeric($notonlynumeric)) {
            $result = $this->db->select('routes', [
            'request', //URI Requested
            'dest', //Destination for redirect or alias
            'type', //If is Alias or is a Redirect
            'redirect', //HTTP Redirect Code (301, 302)
            'canonical', //If route is canonical (For SEO)
            'priority' => DBDefault::RAW('IF(REGEXP_REPLACE(request, \''. $regmatch .'\', 1) = 1, 1, 2)') //Request match has always priority to the destination, to avoid mismatches.
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
        }
        $response = $handler->handle($request);
        return $response;
    }
}
