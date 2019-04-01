<?php

namespace PerSeo;

class Secure
{			
	public static function generate_token($name) {
		$tmpname = 'CSRFToken_'. $name;
		$token = (PHP_MAJOR_VERSION >= 7 ? md5(random_bytes(128)) : md5(mcrypt_create_iv(128, MCRYPT_DEV_URANDOM)));
		$_SESSION[$tmpname] = ($_SESSION[$tmpname] ? $_SESSION[$tmpname] : $token);
		$result['name'] = $tmpname;
		$result['value'] = ($_SESSION[$tmpname] ? $_SESSION[$tmpname] : $token);
		return $result;
	}	
	public function find_first(array $name) {
		foreach($name as $key => $val) {
			$check = strpos($key, 'CSRFToken');
			if ($check !== false) {
				$new_array['name'] = $key;
				$new_array['value'] = $val;
				return $new_array;
			}
		}
		return NULL;
	}	
	public function validate_token($token) {
		$name = $token['name'];
		$token_value = $token['value'];
		$check = $_SESSION[$name];
		$result = hash_equals($check, $token_value);
		unset($_SESSION[$name]);
		return $result;
	}
}