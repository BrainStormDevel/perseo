<?php

namespace PerSeo;

define('DS', DIRECTORY_SEPARATOR);
define('D_ROOT', realpath(__DIR__.DS.'..'.DS.'..'));

class Path
{
	const DS = DS;
	const D_ROOT = D_ROOT;
	const CORE_PATH = D_ROOT.DS.'Core';
	const CONF_PATH = D_ROOT.DS.'config';
	const MOD_PATH = D_ROOT.DS.'modules';
	const LANG_PATH = D_ROOT.DS.'Languages';
	const INC_PATH = D_ROOT.DS.'vendor';
	const INST_PATH = D_ROOT.DS.'install';

	public static $ModuleName = '';

	public static function ModPathTpl() {
		return realpath(self::MOD_PATH . self::DS . self::$ModuleName);
	}	

	public static function LangPath() {
		return realpath(self::MOD_PATH . DIRECTORY_SEPARATOR . self::$ModuleName . DIRECTORY_SEPARATOR .'Languages');
	}
	
	public static function LangAdminPath() {
		return realpath(self::MOD_PATH . DIRECTORY_SEPARATOR . self::$ModuleName . DIRECTORY_SEPARATOR .'Languages'. DIRECTORY_SEPARATOR .'Admin');
	}	
	
	public static function ModuleName() {
		return self::$ModuleName;
	}
	
	public static function ModuleUrlName() {
		return strtolower(self::$ModuleName);
	}	
	
	public static function AdmName() {
		$tmp1 = explode(self::DS, self::$AdmPath);
		return $tmp1[count($tmp1) - 1];
	}	

	public static function ParamsURL($indice) {
		return (self::$ParamsURL[$indice] ? self::$ParamsURL[$indice] : NULL);
	}
	
	public static function ViewsPath() {
		return realpath(self::MOD_PATH . DIRECTORY_SEPARATOR . self::$ModuleName . DIRECTORY_SEPARATOR .'Views');
	}	

	public static function SiteName($request)
	{
		return '//'. $_SERVER['HTTP_HOST'] . $request->getUri()->getBasePath();
	}
	
	public static function MY($arg)
	{
		switch($arg) {
				case 'HOST':
				return '//' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos( $_SERVER['SCRIPT_NAME'], '/'));
				break;
				case 'ADM_HOST':
				if (self::$AdmPath != '') {
					return '//' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos( $_SERVER['SCRIPT_NAME'], '/')) . '/' . strtolower(\PerSeo\Path::$AdmPath);
				}
				else {
					return NULL;
				}
				break;				
				case 'PATH':
				return rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				break;
				case 'DOMAIN':
				return $_SERVER['HTTP_HOST'];
				break;
				default:
				return NULL;
				break;
		}
	}	
}