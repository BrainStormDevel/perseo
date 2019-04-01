<?php

namespace PerSeo;

class Language
{
	public static function Get() {
		if (isset($_COOKIE['lang'])) {
			$lang = trim(preg_replace('/[^a-z]/', '', strtolower($_COOKIE['lang'])));
			$langfile = \PerSeo\Path::LANG_PATH . \PerSeo\Path::DS . $lang . '.lng';
			if (file_exists($langfile)) { return $lang; }
			else {
				$lang = trim(preg_replace('/[^a-z]/', '', strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))));
				$langfile = \PerSeo\Path::LANG_PATH . \PerSeo\Path::DS . $lang . '.lng';
				if (file_exists($langfile)) { return $lang; }
				else {
					if (defined('LANG_DEFAULT')) {
						return strtolower(LANG_DEFAULT);
					}
				}
			}
		}
		else {
			$lang = trim(preg_replace('/[^a-z]/', '', strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))));
			$langfile = \PerSeo\Path::LANG_PATH . \PerSeo\Path::DS . $lang . '.lng';
			if (file_exists($langfile)) { return $lang; }
			else {
				if (defined('LANG_DEFAULT')) {
					return strtolower(LANG_DEFAULT);
				}
			}
		}
	}
}