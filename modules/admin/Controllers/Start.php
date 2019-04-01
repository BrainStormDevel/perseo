<?php

namespace Admin\Controllers;

class Start
{
	public function main()
    {		
			$mylogin = new \PerSeo\Login();
			$test = $mylogin->islogged('admins');
			if (!$test) {
				header('Location: '. \PerSeo\Router::MY('HOST').'/'. strtolower(\PerSeo\Router::ModuleName()) .'/login/');
			}
			else {
				$tplmain = \PerSeo\Router::ViewsPath() . \PerSeo\Router::DS .'Admin'. \PerSeo\Router::DS .'prova.tpl';
				\Admin\Models\Panel::main($tplmain);
			}
    }
}