<?php

namespace Wizard\Controllers;

use Exception;

class Test
{
	public static function main() {
			$CSRFToken = \PerSeo\Secure::generate_token('Wizard');
			$db = new \PerSeo\DB("mysql", \PerSeo\Request::POST('dbname', 'user'), \PerSeo\Request::POST('dbhost', 'user'), \PerSeo\Request::POST('dbuser', 'user'), \PerSeo\Request::POST('dbpass', 'pass'));
			$error = $db->err();
			if (isset($error['msg'])) {
				$token = Array(
					"CSRFname" =>  $CSRFToken['name'],
					"CSRFToken" =>  $CSRFToken['value']
				);			
				echo json_encode(array_merge($error, $token));
			}
			else {
				
				$result = Array(
					"err" => 0,
					"code" => 0,
					"msg" => "ok",
					"CSRFname" =>  $CSRFToken['name'],
					"CSRFToken" =>  $CSRFToken['value']
				);
				echo json_encode($result);
			}
	}
	
}