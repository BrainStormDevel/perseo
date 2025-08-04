<?php

namespace Modules\wizard\Controllers;

use Exception;
use PerSeo\DB;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modules\wizard\Classes\Config;
use Modules\wizard\Classes\InstallSQLDB;
use Modules\wizard\Classes\InstallSQLiteDB;

class Install
{
	protected $container;
	protected $driver;
	protected $host;
	protected $name;
	protected $user;
	protected $pass;
	protected $encoding;
	protected $port;
	protected $tbprefix;
	protected $salt;
	protected $admin; 
	protected $email; 
	protected $password;

    public function __construct(ContainerInterface $container)
    {
		$this->container = $container;
    }

    public function __invoke(Request $request, Response $response): Response
	{	
		try {
			$configfile = $this->container->get('settings_root') .'/config';
			$fileconf = $configfile . DIRECTORY_SEPARATOR .'settings.php';
			$post = $request->getParsedBody();
			$config = new Config($fileconf);
			$resultc = json_decode($config->base($post));
			if ((int) $resultc->code != 0) {
				throw new Exception($resultc->msg, $resultc->code);
			}
			if ($post['driver'] == 'mysql') {
				$installsqldb = new InstallSQLDB($fileconf);
				$resultdb = json_decode($installsqldb->createDB($post['driver'], $post['dbhost'], $post['dbname'], $post['dbuser'], $post['dbpass'], $post['prefix'], $post['dbencoding']));
				if ((int) $resultdb->code != 0) {
					throw new Exception($resultdb->msg, (int) $resultdb->code);
				}
			}
			elseif ($post['driver'] == 'sqlite') {
				$installsqlitedb = new InstallSQLiteDB($fileconf, $this->container->get('settings_root'));
				$resultdb = json_decode($installsqlitedb->createDB($post['driver'], $post['dbfile'], $post['prefix']));
				if ((int) $resultdb->code != 0) {
					throw new Exception($resultdb->msg, (int) $resultdb->code);
				}
			}
			session_destroy();
			$result = array(
                'code' => '0',
                'msg' => 'OK'
            );
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        $response->getBody()->write(json_encode($result));
		return $response;
    }
}