<?php

namespace Utenti\Controllers\Admin;

class Start
{
	public function main()
    {
			$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Router::LangAdminPath());
			$lang->module('title');
			$lang->module('body');
			$vars = $lang->vars();
			$tplmain = \PerSeo\Router::ViewsPath() . \PerSeo\Router::DS .'Admin'. \PerSeo\Router::DS .'main.tpl';
			\Admin\Models\Panel::main($tplmain, $vars);
    }
}