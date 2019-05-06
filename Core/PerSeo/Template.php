<?php

namespace PerSeo;

class Template
{	
	const PRODNAME = 'PerSeo';
	
	const PRODVER = '1.0';
	
	const ENCODING = 'utf-8';

	public static function vars() {
		$vars = array();
		$vars['ProdName'] = PRODNAME;
		$vars['ProdVer'] = PRODVER;
		$vars['encoding'] = ENCODING;
		$vars['language'] = \PerSeo\Language::Get();
		$vars['ModuleName'] = \PerSeo\Path::ModuleName();
		$vars['ModuleUrlName'] = strtolower(\PerSeo\Path::ModuleName());
		return $vars;
	}
}