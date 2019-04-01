<?php

namespace Admin\Controllers;

class Logout {
	public static function main() {
		$mylogin = new \PerSeo\Login();
		echo $mylogin->logout('admins');
	}
}