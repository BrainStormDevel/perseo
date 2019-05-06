<?php

namespace admin\Controllers;

class Menu
{
	public function listall()
    {
		$menus = array();
		$directory = \PerSeo\Path::MOD_PATH;
		if(is_dir($directory)) {
			$scan = scandir($directory);
			unset($scan[0], $scan[1]); //unset . and ..
			foreach($scan as $dir) {
				$menu = $dir . DIRECTORY_SEPARATOR .'Views'. DIRECTORY_SEPARATOR .'admin/menu.tpl';
				if(is_dir($directory . DIRECTORY_SEPARATOR . $dir) && file_exists($directory . DIRECTORY_SEPARATOR . $menu)) {
					array_push($menus, $menu);
				}
			}
		}
		return $menus;
    }
}