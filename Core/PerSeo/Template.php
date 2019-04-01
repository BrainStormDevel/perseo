<?php

namespace PerSeo;

use Smarty;
use Smarty_Security;

class Template
{	
	const PRODNAME = 'PerSeo';
	
	const PRODVER = '1.0';
	
	const ENCODING = 'utf-8';

	public static function show($dir, $page, $vars=null, bool $caching=false) {
		$_method = new Smarty();
		//$_method->enableSecurity();
		$_method->security_settings = array(
			'INCLUDE_ANY' => true
		);
		$_method->setTemplateDir($dir);
		$_method->setCompileDir(\PerSeo\Path::D_ROOT . \PerSeo\Path::DS .'cache'. \PerSeo\Path::DS .'compile');
		$_method->setCacheDir(\PerSeo\Path::D_ROOT . \PerSeo\Path::DS .'cache'. \PerSeo\Path::DS .'tmp');
		if ($caching) {
			$_method->caching = true;
			$_method->cache_lifetime = $caching;
		}
		else {
			$_method->caching = false;
		}
		if ($vars) {
		foreach ($vars as $key => $value) {
			$_method->assign($key, $value);
		}
		}
		$_method->assign('ProdName', self::PRODNAME);
		$_method->assign('ProdVer', self::PRODVER);
		$_method->assign('host', \PerSeo\Path::MY('HOST'));
		$_method->assign('encoding', self::ENCODING);
		$_method->assign('ModuleName', \PerSeo\Path::ModuleName());
		$_method->assign('ModuleUrlName', \PerSeo\Path::ModuleUrlName());
		$_method->assign('lang', \PerSeo\Language::Get());
		//$_method->assign('cookie_path', COOKIE_PATH);
		$_method->display($page);
	}
}