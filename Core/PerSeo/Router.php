<?php

namespace PerSeo;

use Slim\App;

define('DS', DIRECTORY_SEPARATOR);
define('D_ROOT', realpath(__DIR__.DS.'..'.DS.'..'));

class Router extends App
{	
	public function __construct() {
	try {
		//$reqServer = $_SERVER;
		//$reqServer['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'], 0, strrpos( $_SERVER['SCRIPT_NAME'], '/')) . '/';
		parent::__construct([
		'settings' => [
			'determineRouteBeforeAppMiddleware' => true,
			'displayErrorDetails' => true,
			'addContentLengthHeader' => false
		]/*,
		'request' => \Slim\Http\Request::createFromEnvironment(
            \Slim\Http\Environment::mock(
				$reqServer
            )
        )*/
    ]);
	} catch (Exception $e) {
		$this->err = Array (
			"err" => 1,
			"code" => $e->getCode(),
			"msg" => $e->getMessage()
		);
	}
    }
	public function addRoute($regex, $method, $nameroute) {
		try {
			$this->get($regex, $method)->setName($nameroute);
			$ModName = explode("\\", $method);
			self::$ModuleName = $ModName[0];
			self::$ModuleUrlName = strtolower($ModName[0]);
			self::$ModPath = realpath(self::MOD_PATH . self::DS . self::$ModuleName);
			self::$ViewsPath = realpath(self::MOD_PATH . self::DS . self::$ModuleName . self::DS .'Views');
			self::$LangPath = realpath(self::MOD_PATH . self::DS . self::$ModuleName . self::DS .'Languages');
			self::$LangAdminPath = realpath(self::MOD_PATH . self::DS . self::$ModuleName . self::DS .'Languages' . self::DS .'Admin');
		} catch (Exception $e) {
			$this->err = Array (
			"err" => 1,
			"code" => $e->getCode(),
			"msg" => $e->getMessage()
			);
		}
	}
	public function err() {
		return $this->err;
	}	
}