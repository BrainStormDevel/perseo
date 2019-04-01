<?php

namespace Wizard\Controllers;

class Start
{
	public function main($request, $response, $args, $app)
    {	
			//$app->add(new \Slim\Csrf\Guard);
			$write = (is_writable(\PerSeo\Path::CONF_PATH) ? "ok" : "no");
			$css = \PerSeo\Library::css('twitter-bootstrap', 'css/bootstrap.min', '3.3.7');
			$css .= \PerSeo\Library::css('prettify', 'prettify.min', 'r298');
			$css .= \PerSeo\Library::css('bootstrap-select', 'css/bootstrap-select.min', '1.12.4');
			$css .= \PerSeo\Library::css('font-awesome', 'css/font-awesome.min', '4.7.0');

			$js = \PerSeo\Library::js('jquery', 'jquery.min', '3.3.1');
			$js .= \PerSeo\Library::js('twitter-bootstrap', 'js/bootstrap.min', '3.3.7');
			$js .= \PerSeo\Library::js('twitter-bootstrap-wizard', 'jquery.bootstrap.wizard.min', '1.2');		
			$js .= \PerSeo\Library::js('prettify', 'prettify.min', 'r298');
			$js .= \PerSeo\Library::js('bootstrap-select', 'js/bootstrap-select.min', '1.12.4');
			$js .= \PerSeo\Library::js('jquery.complexify.js', 'jquery.complexify.min', '0.5.1');
			$js .= \PerSeo\Library::js('js-cookie', 'js.cookie.min', '2.2.0');
			
			$G_SECRET = 'G_SECRET_'. \PerSeo\Path::MY('DOMAIN');
			$F_SECRET = 'F_SECRET_'. \PerSeo\Path::MY('DOMAIN');
			//$CSRFToken = \PerSeo\Secure::generate_token('Wizard');
			$nameKey = $app->csrf->getTokenNameKey();
			$valueKey = $app->csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
			$vars1 = array(
				'title' => 'Welcome to',
				'folder' => \PerSeo\Path::CONF_PATH,
				'CSRFname' =>  $CSRFToken['name'],
				'CSRFToken' =>  $CSRFToken['value'],
				'write' => $write,
				'lang' => \PerSeo\Language::Get(),
				'main_host' => str_replace("/install", "", \PerSeo\Path::MY('HOST')),
				'cookiepath' => $_SERVER['REQUEST_URI'],
				'css' => $css,
				'js' => $js
			);
			$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
			$lang->module('title');
			$lang->module('body');
			$vars = array_merge($vars1, $lang->vars());
			\PerSeo\Template::show(\PerSeo\Path::ViewsPath(), 'default.tpl', $vars, false);
    }
}