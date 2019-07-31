<?php

namespace users\Controllers;

class Locale
{
    public static function get($lang)
    {
		switch ($lang) {
		case 'it':
			return 'Italian';
			break;
		case 'en':
			return 'English';
			break;
		}
    }
}