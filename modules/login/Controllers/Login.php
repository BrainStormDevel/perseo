<?php

namespace login\Controllers;

class Login {
	public static function check($container) {
		$mylogin = new \PerSeo\Login();
		$typesArray = array(
			'admins',
			'users'
		);
		$type = (in_array($container->Sanitizer->POST('type', 'alpha'), $typesArray) ? $container->Sanitizer->POST('type', 'alpha') : 'admins');
		echo $mylogin->login($container->Sanitizer->POST('username', 'user'), $container->Sanitizer->POST('password', 'pass'), $type, ($container->Sanitizer->POST('rememberme', 'int') == 1 ? false : true));
	}
}