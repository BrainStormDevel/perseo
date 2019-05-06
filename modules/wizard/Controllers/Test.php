<?php

namespace wizard\Controllers;

use Exception;

class Test
{
	public static function main($container) {
			$db = new \PerSeo\DB("mysql", $container->Sanitizer->POST('dbname', 'pass'), $container->Sanitizer->POST('dbhost', 'user'), $container->Sanitizer->POST('dbuser', 'user'), $container->Sanitizer->POST('dbpass', 'pass'));
			$error = $db->err();
			if (isset($error['msg'])) {		
				echo json_encode($error);
			}
			else {
				$result = Array(
					"err" => 0,
					"code" => 0,
					"msg" => "ok"
				);
				echo json_encode($result);
			}
	}
	
}