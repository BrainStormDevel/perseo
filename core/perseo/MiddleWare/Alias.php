<?php

namespace PerSeo\MiddleWare;

use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use PerSeo\DB;

class Alias implements Middleware
{
    protected $app;
    protected $db;
	protected $settings;

    public function __construct(App $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->db = ($container->has('db') ? $container->get('db') : null);
		$this->settings = ($container->has('settings_db') ? $container->get('settings_db')['default'] : ['type' => 'nodb']);
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
				if($this->settings['type'] == 'mysql') {
					$result = $this->db->select('routes', [
						'request', //URI Requested
						'dest', //Destination for redirect or alias
						'type', //If is Alias or is a Redirect
						'redirect', //HTTP Redirect Code (301, 302)
						'canonical', //If route is canonical (For SEO)
						'priority' => DB::RAW('IF(REGEXP_REPLACE(request, :regmatch, 1) = 1, 1, 2)', [
								":regmatch" => $regmatch
						]) //Request match has always priority to the destination, to avoid mismatches.
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
				}
				elseif($this->settings['type'] == 'pgsql') {
					$tableName = $this->settings['prefix'] .'routes';
					$sql = "SELECT 
							request,
							dest,
							type,
							redirect,
							canonical,
							CASE WHEN request ~ :regmatch THEN 1 ELSE 2 END AS priority
						FROM \"$tableName\"
						WHERE request ~ :regmatch OR dest ~ :regmatch
						ORDER BY priority";
					$stmt = $this->db->pdo->prepare($sql);
					$stmt->execute([':regmatch' => $regmatch]);
					
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				}
				else if($this->settings['type'] == 'sqlite') {
					$result = $this->db->select('routes', [
						'request', //URI Requested
						'dest', //Destination for redirect or alias
						'type', //If is Alias or is a Redirect
						'redirect', //HTTP Redirect Code (301, 302)
						'canonical', //If route is canonical (For SEO)
						'priority' => DB::RAW('CASE WHEN request REGEXP :regmatch THEN 1 ELSE 2 END', [
								":regmatch" => $regmatch
						]) //Request match has always priority to the destination, to avoid mismatches.
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
				}
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
