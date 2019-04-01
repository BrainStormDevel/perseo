<?php

namespace Admin\Controllers;

class Checklogin {
	public static function main() {
		$mylogin = new \PerSeo\Login();
		echo $mylogin->login(\PerSeo\Request::POST('username'), \PerSeo\Request::POST('password'), 'admins', (\PerSeo\Request::POST('rememberme') == 1 ? false : true));
	}
}