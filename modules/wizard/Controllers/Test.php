<?php

namespace Modules\wizard\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PerSeo\DB;

final class Test
{

    public function __invoke(Request $request, Response $response): Response {
        try {
			$post = $request->getParsedBody();
            $db = new DB([
                'database_type' => (string) $post['driver'],
                'database_name' => (string) $post['dbname'],
                'server' => (string) $post['dbhost'],
                'username' => (string) $post['dbuser'],
                'password' => (string) $post['dbpass'],
                'charset' => (string) $post['charset']
            ]);
            $info = $db->info();
			$version = (string) $info['version'];
			if(strpos($version, 'MariaDB') !== false){
				$explode = explode("-", $version);
				$version = $explode[0];
				if ($version < '10.0.5') {
                    throw new \Exception('Minimum requirements: Mariadb 10.0.5',0001);
                }
			}
            else {
                if ($version < '8.0.0') {
                    throw new \Exception('Minimum requirements: Mysql 8.0.0',0001);
                }
            }
            $result = Array(
                "err" => 0,
                "code" => 0,
                "msg" => "ok"
            );
        } catch (\Exception $e) {
            $result = Array(
                "err" => 1,
                "code" => $e->getCode(),
                "msg" => $e->getMessage()
            );
        }	
		$response->getBody()->write(json_encode($result));
		return $response;	
    }
}