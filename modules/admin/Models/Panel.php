<?php

namespace Admin\Models;

class Panel
{
	public function main($page, $vars = NULL, $header = NULL, $footer = NULL)
    {		
			$mylogin = new \PerSeo\Login();
			$test = $mylogin->islogged('admins');
			
			if (!$test) {
				header('Location: '. \PerSeo\Router::MY('HOST').'/admin/login/');
			}
			else {
				
				$adminpath = realpath(\PerSeo\Router::MOD_PATH . \PerSeo\Router::DS . 'Admin' . \PerSeo\Router::DS .'Views');
				$CSRFToken = \PerSeo\Secure::generate_token('Admin');
				$varlang = array(
				    'AdminModuleName' => 'admin',
					'title' => SITENAME,
					'username' => \PerSeo\Login::username(),
					'privileges' => \PerSeo\Login::privileges(),
					'superuser' => \PerSeo\Login::superuser(),
					'adm_host' => \PerSeo\Router::MY('ADM_HOST'),
					'CSRFname' =>  $CSRFToken['name'],
					'CSRFToken' =>  $CSRFToken['value'],
					'css' => $css,
					'js' => $js,
					'body_page' => $page,
					'header' => $header,
					'footer' => $footer,
					'menu' => self::RecursiveDir(\PerSeo\Router::MOD_PATH .'/', 'menu.tpl')
				);
				$var = ($vars != NULL ? array_merge($vars, $varlang) : $varlang);	
				\PerSeo\Template::show($adminpath, 'panel.tpl', $var, false);
			}
    }
	public function RecursiveDir($dir, $file)
    {		
		$Directory = new \RecursiveDirectoryIterator(realpath($dir));
		$Iterator = new \RecursiveIteratorIterator($Directory);
		$Regex = new \RegexIterator($Iterator, '/^.+\\'. $file .'$/i', \RecursiveRegexIterator::GET_MATCH);
		$ToArray = iterator_to_array($Regex, true);
		$i=0;
		$result = array();
		foreach ($ToArray as &$value) {
			$result[$i] = $value[0];
			$i++;
		}
		return $result;
    }		
}