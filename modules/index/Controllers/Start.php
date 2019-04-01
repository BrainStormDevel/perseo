<?php

namespace index\Controllers;

class Start
{
	public function main($id)
    {		
			$css = \PerSeo\Library::css('normalize', 'normalize.min', '8.0.0');
			$css .= \PerSeo\Library::css('twitter-bootstrap', 'css/bootstrap.min', '3.3.7');
			$css .= \PerSeo\Library::css('owl-carousel', 'owl.carousel.min', '1.3.3');
			$css .= \PerSeo\Library::css('animate.css', 'animate.min', '3.5.2');
			$css .= \PerSeo\Library::css('font-awesome', 'css/font-awesome.min', '4.7.0');

			$js = \PerSeo\Library::js('jquery', 'jquery.min', '1.12.4');
			$js .= \PerSeo\Library::js('owl-carousel', 'owl.carousel.min', '1.3.3');
			$js .= \PerSeo\Library::js('twitter-bootstrap', 'js/bootstrap.min', '3.3.7');
			$js .= \PerSeo\Library::js('wow', 'wow.min', '1.1.2');
			$js .= \PerSeo\Library::js('TypewriterJS', 'typewriter.min', '1.0.0');
			$js .= \PerSeo\Library::js('jquery-one-page-nav', 'jquery.nav.min', '3.0.0');
			/*$js .= Library::js('twitter-bootstrap', 'js/bootstrap.min', '3.3.7');
			$js .= Library::js('bootstrap-select', 'js/bootstrap-select.min', '1.12.4');
			$js .= Library::js('iCheck', 'icheck.min', '1.0.2');*/
			
			$G_SECRET = 'G_SECRET_'. \PerSeo\Router::MY('DOMAIN');
			$F_SECRET = 'F_SECRET_'. \PerSeo\Router::MY('DOMAIN');

			/*$vars1 = array(
				'title' => SITENAME,
				'css' => $css,
				'js' => $js,
				'gsecret' => (constant($G_SECRET) ? 1 : 0),
				'fsecret' => (constant($F_SECRET) ? 1 : 0)
			);
			$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Path::LangPath());
			$lang->module('title');
			$lang->module('body');
			$varlang = $lang->vars();
			$vars = array_merge($vars1, $varlang);
			\PerSeo\Template::show(\PerSeo\Path::ViewsPath(), 'index.tpl', $vars, false);*/
			echo "questa è la index $id";
    }
}