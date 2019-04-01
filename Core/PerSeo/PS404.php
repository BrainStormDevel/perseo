<?php

namespace PerSeo;

class PS404
{
	public static function show() {
		header('HTTP/1.0 404 Not Found', true, 404);
		$dir = \PerSeo\Router::D_ROOT . \PerSeo\Router::DS . 'templates';
		$test1 = parse_url(\PerSeo\Router::MY('HOST'));
		$vars = array(
			'title' => SITENAME,
			'host' => \PerSeo\Router::MY('HOST')
		);
		\PerSeo\Template::show($dir, '404.tpl', $vars, false, 300);
	}
}