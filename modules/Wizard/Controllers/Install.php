<?php

namespace Wizard\Controllers;

use Exception;

class Install
{
	private static $host = "";
	private static $name = "";
	private static $user = "";
	private static $pass = "";
	private static $encoding = "";
	private static $port = "";
	private static $tbprefix = "";
	private static $salt = "";
	
	public static function main() {
			$fileconf = \PerSeo\Router::CONF_PATH . \PerSeo\Router::DS . 'config.php';
			try {
				$myfile = fopen($fileconf, "w");
				$content = "<?php\n";
				$content .= "define('DBHOST', '". \PerSeo\Request::POST('dbhost', 'user') ."');\n";
				$content .= "define('DB', 'mysql');\n";
				$content .= "define('DBPATH', NULL);\n";
				$content .= "define('DBNAME', '". \PerSeo\Request::POST('dbname', 'user') ."');\n";
				$content .= "define('DBUSER', '". \PerSeo\Request::POST('dbuser', 'user') ."');\n";
				$content .= "define('DBPASS', '". \PerSeo\Request::POST('dbpass', 'pass') ."');\n";
				$content .= "define('DBENCODING', '". \PerSeo\Request::POST('dbencoding', 'user') ."');\n";
				$content .= "define('SITENAME', '". \PerSeo\Request::POST('title') ."');\n";
				$content .= "define('POSTFIX', '.html');\n";
				$content .= "define('ENCODING', '". \PerSeo\Request::POST('encoding') ."');\n";
				$content .= "define('LANG_DEFAULT', '". \PerSeo\Request::POST('lang', 'aplha') ."');\n";
				$content .= "define('ADM_COOKNAME', '". \PerSeo\Request::POST('cookadm', 'user') ."');\n";
				$content .= "define('USR_COOKNAME', '". \PerSeo\Request::POST('cookusr', 'user') ."');\n";
				$content .= "define('MAX_U', '16');\n";
				$content .= "define('MAX_P', '20');\n";
				$content .= "define('MAX_E', '40');\n";
				$content .= "define('MAX_T', '100');\n";
				$content .= "define('MAX_FLOOD', '1');\n";
				$content .= "define('CRYPT_SALT', '". \PerSeo\Request::POST('salt') ."');\n";
				$content .= "define('TBL_', '". \PerSeo\Request::POST('prefix', 'user') ."');\n";
				$content .= "define('COOKIE_EXPIRE', '". \PerSeo\Request::POST('cookexp', 'int') ."');\n";
				$content .= "define('COOKIE_MAX_EXPIRE', '". \PerSeo\Request::POST('cookmaxexp', 'int') ."');\n";
				$content .= "define('COOKIE_PATH', '". \PerSeo\Request::POST('cookpath') ."');\n";
				$content .= "define('COOKIE_SECURE', false);\n";
				$content .= "define('COOKIE_HTTP', true);\n";
				fwrite($myfile, $content);
				self::$host = \PerSeo\Request::POST('dbhost', 'user');
				self::$name = \PerSeo\Request::POST('dbname', 'user');
				self::$user = \PerSeo\Request::POST('dbuser', 'user');
				self::$pass = \PerSeo\Request::POST('dbpass', 'pass');
				self::$encoding = \PerSeo\Request::POST('dbencoding', 'user');
				self::$port = '3306';
				self::$tbprefix = \PerSeo\Request::POST('prefix', 'user');
				self::$salt = \PerSeo\Request::POST('salt');
				$result1 = self::createdb(\PerSeo\Request::POST('admin', 'user'), \PerSeo\Request::POST('email', 'email'), \PerSeo\Request::POST('password', 'pass'), \PerSeo\Request::POST('salt'));
				session_unset();
				session_destroy();
			}
			catch (Exception $e) {
				$result1 = array(
					'code' => $e->getCode(),
					'msg' => $e->getMessage()
				);
			}
			$CSRFToken = \PerSeo\Secure::generate_token('Wizard');
			$token = Array(
				"CSRFname" =>  $CSRFToken['name'],
				"CSRFToken" =>  $CSRFToken['value']
			);
			$result = array_merge($result1, $token);
			echo json_encode($result);
	}
	private static function createdb($user, $email, $pass, $salt) {
		try {
		$db = new \PerSeo\DB('mysql', self::$name, self::$host, self::$user, self::$pass, self::$tbprefix, self::$encoding);
		$db->query("CREATE TABLE IF NOT EXISTS ". self::$tbprefix ."admins (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, privilegi int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
		$db->query("CREATE TABLE IF NOT EXISTS ". self::$tbprefix ."cookies (id int(100) NOT NULL auto_increment, uid int(100) NOT NULL, uuid varchar(255) COLLATE utf8_unicode_ci NOT NULL, privilegi int(2) NOT NULL, type varchar(10) COLLATE utf8_unicode_ci NOT NULL, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, auth_token varchar(255) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (id), UNIQUE KEY uuid (uuid)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
		$data = $db->insert("admins", [
			"user" => $user,
			"pass" => \PerSeo\Login::create_hash($pass),
			"email" => $email,
			"superuser" => \PerSeo\Login::encrypt($user, $salt),
			"privilegi" => '1',
			"stato" => '0'
		]);
		$result = array(
			'code' => '0',
			'msg' => 'OK'
		);
		} catch (Exception $e) {
			$result = array(
				'code' => $e->getCode(),
				'msg' => $e->getMessage()
			);
		}
		return $result;
	}
}