<?php

namespace Modules\wizard\Controllers;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modules\wizard\Classes\TestMySQLDB;
use Modules\wizard\Classes\TestSQLiteDB;

final class Test
{
	private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
		$this->container = $container;
    }

    public function __invoke(Request $request, Response $response): Response {
        try {
			$post = $request->getParsedBody();
			if ($post['driver'] == 'mysql') {
				$test = new TestMySQLDB();
				$result = json_decode($test($post));
				if ($result->err > 0) { throw new Exception($result->msg, (int) $result->code); }
			}
			elseif ($post['driver'] == 'sqlite') {
				$configpath = $this->container->get('settings_root') .'/config';
				$test = new TestSQLiteDB($configpath);
				$result = json_decode($test($post));
				if ($result->err > 0) { throw new Exception($result->msg, (int) $result->code); }
			}
			else {
				$result = array(
					"err" => 0,
					"code" => 0,
					"msg" => "ok"
				);
			}
        } catch (Exception $e) {
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