<?php

namespace Utenti\Controllers\Admin;

class Gestadmins
{
	public function main()
    {
			$CSRFToken = \PerSeo\Secure::generate_token('Admin');
			$lang = new \PerSeo\Translator(\PerSeo\Language::Get(), \PerSeo\Router::LangAdminPath());
			$lang->module('title');
			$lang->module('body');
			$langvars = $lang->vars();
			$vars_tpl = array(
				'admtot' => count(\Utenti\Models\Users::list_admins()),
				'admins' => \Utenti\Models\Users::list_admins(),
				'AdminsCSRFname' =>  $CSRFToken['name'],
				'AdminsCSRFToken' =>  $CSRFToken['value']
			);
			$vars = array_merge($langvars, $vars_tpl);
			$viewspath = \PerSeo\Router::ViewsPath() . \PerSeo\Router::DS .'Admin'. \PerSeo\Router::DS;
			$tplmain = $viewspath .'gestadmins.tpl';
			$tplhead = $viewspath .'header.tpl';
			$tplfoot = $viewspath .'footer.tpl';
			\Admin\Models\Panel::main($tplmain, $vars, $tplhead, $tplfoot);
    }
	public function add_user()
    {
		echo \Utenti\Models\Users::add_user('admins', \PerSeo\Request::POST('id', 'int'), \PerSeo\Request::POST('username'), \PerSeo\Request::POST('password'), \PerSeo\Request::POST('email'), \PerSeo\Request::POST('privileges', 'int'));
    }
	public function del_user()
    {
		echo \Utenti\Models\Users::del_user('admins', \PerSeo\Request::POST('id'));
    }	
}