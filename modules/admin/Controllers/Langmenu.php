<?php

namespace admin\Controllers;

class Langmenu
{
	
	private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
	public function get()
	{
		if ($this->container->has('modules.name')) {
			$result = array();
			$modules = $this->container->get('modules.name');
			foreach ($modules as $module) {
				$name = $module['name'];
				if (!empty($module['menu'])) {
					$file = \PerSeo\Path::MOD_PATH . DIRECTORY_SEPARATOR . $module['name'] . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR .'admin'. DIRECTORY_SEPARATOR . $this->container->get('current.language') .'.lng';
					if (file_exists($file)) {
						$currfile = file_get_contents($file);
						$tmp = json_decode($currfile, true);
						$result[$name] = $tmp['menu'];
					}
				}
			}
			return $result;
		}
    }
}