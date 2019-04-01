<?php

namespace Admin\Controllers;

class Login
{
	public function main()
    {		
			$css = \PerSeo\Library::css('twitter-bootstrap', 'css/bootstrap.min', '3.3.7');
			$css .= \PerSeo\Library::css('bootstrap-select', 'css/bootstrap-select.min', '1.12.4');
			$css .= \PerSeo\Library::css('font-awesome', 'css/font-awesome.min', '4.7.0');
			$css .= \PerSeo\Library::css('ionicons', 'css/ionicons.min', '4.0.0-9');
			$css .= \PerSeo\Library::css('admin-lte', 'css/AdminLTE.min', '2.4.2');
			$css .= \PerSeo\Library::css('iCheck', 'skins/square/blue', '1.0.2');
			$css .= \PerSeo\Library::css('flag-icon-css', 'css/flag-icon.min', '2.9.0');

			$js = \PerSeo\Library::js('jquery', 'jquery.min', '3.3.1');
			$js .= \PerSeo\Library::js('twitter-bootstrap', 'js/bootstrap.min', '3.3.7');
			$js .= \PerSeo\Library::js('bootstrap-select', 'js/bootstrap-select.min', '1.12.4');
			$js .= \PerSeo\Library::js('iCheck', 'icheck.min', '1.0.2');
			
			$G_SECRET = 'G_SECRET_'. \PerSeo\Router::MY('DOMAIN');
			$F_SECRET = 'F_SECRET_'. \PerSeo\Router::MY('DOMAIN');
			$CSRFToken = \PerSeo\Secure::generate_token('Login');
			$vars1 = array(
				'title' => SITENAME,
				'CSRFname' =>  $CSRFToken['name'],
				'CSRFToken' =>  $CSRFToken['value'],
				'css' => $css,
				'js' => $js,
				'gsecret' => (constant($G_SECRET) ? 1 : 0),
				'fsecret' => (constant($F_SECRET) ? 1 : 0)
			);
			$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Router::LangPath());
			$lang->module('title');
			$lang->module('body');
			$varlang = $lang->vars();
			$vars = array_merge($vars1, $varlang);
			\PerSeo\Template::show(\PerSeo\Router::ViewsPath(), 'login.tpl', $vars, false);
    }
}