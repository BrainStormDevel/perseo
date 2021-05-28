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