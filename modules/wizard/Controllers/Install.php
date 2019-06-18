<?php

namespace wizard\Controllers;

use Exception;

class Install
{

    private static $driver = "";
    private static $host = "";
    private static $name = "";
    private static $user = "";
    private static $pass = "";
    private static $encoding = "";
    private static $port = "";
    private static $tbprefix = "";
    private static $salt = "";

    public static function main($container)
    {
        $fileconf = \PerSeo\Path::CONF_PATH . \PerSeo\Path::DS . 'settings.php';
        try {
            $myfile = fopen($fileconf, "w");
            $content = "<?php\n\n";
            $content .= "return [
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => true,
    'settings.addContentLengthHeader' => false,
    'settings.global' => [
        'sitename' => '" . $container->get('Sanitizer')->POST('title') . "',
        'encoding' => '" . $container->get('Sanitizer')->POST('encoding') . "',
        'language' => '" . $container->get('Sanitizer')->POST('lang', 'alpha') . "'
    ],
    'settings.secure' => [
        'crypt_salt' => '" . $container->get('Sanitizer')->POST('salt', 'alpha') . "',
        'max_u' => '16',
        'max_p' => '20',
        'max_e' => '40',
        'max_t' => '100'
    ],
    'settings.cookie' => [
		'admin' => '" . $container->get('Sanitizer')->POST('cookadm', 'user') . "',
		'user' => '" . $container->get('Sanitizer')->POST('cookusr', 'user') . "',
        'cookie_exp' => '" . $container->get('Sanitizer')->POST('cookexp', 'alpha') . "',
        'cookie_max_exp' => '" . $container->get('Sanitizer')->POST('cookmaxexp', 'alpha') . "',
        'cookie_path' => '" . $container->get('Sanitizer')->POST('cookpath') . "',
        'cookie_secure' => false,
        'cookie_http' => true
    ],
    'settings.database' => [
        'default' => [
            'driver' => '" . $container->get('Sanitizer')->POST('driver', 'user') . "',
            'host' => '" . $container->get('Sanitizer')->POST('dbhost', 'user') . "',
            'database' => '" . $container->get('Sanitizer')->POST('dbname', 'pass') . "',
            'username' => '" . $container->get('Sanitizer')->POST('dbuser', 'user') . "',
            'password' => '" . $container->get('Sanitizer')->POST('dbpass', 'pass') . "',
            'prefix' => '" . $container->get('Sanitizer')->POST('prefix', 'user') . "',
            'charset' => '" . $container->get('Sanitizer')->POST('dbencoding', 'user') . "',
            'port' => 3306

        ]
    ]
];";
            fwrite($myfile, $content);
            fclose($myfile);
            self::$driver = $container->get('Sanitizer')->POST('driver', 'user');
            self::$host = $container->get('Sanitizer')->POST('dbhost', 'user');
            self::$name = $container->get('Sanitizer')->POST('dbname', 'pass');
            self::$user = $container->get('Sanitizer')->POST('dbuser', 'user');
            self::$pass = $container->get('Sanitizer')->POST('dbpass', 'pass');
            self::$encoding = $container->get('Sanitizer')->POST('dbencoding');
            self::$port = '3306';
            self::$tbprefix = $container->get('Sanitizer')->POST('prefix', 'user');
            self::$salt = $container->get('Sanitizer')->POST('salt', 'alpha');
            $result = self::createdb($container->get('Sanitizer')->POST('admin', 'user'),
                $container->get('Sanitizer')->POST('email', 'email'),
                $container->get('Sanitizer')->POST('password', 'pass'),
                $container->get('Sanitizer')->POST('salt', 'alpha'));
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        echo json_encode($result);
    }

    private static function createdb($user, $email, $pass, $salt)
    {
        try {
            $db = new \PerSeo\DB([
                'database_type' => self::$driver,
                'database_name' => self::$name,
                'server' => self::$host,
                'username' => self::$user,
                'password' => self::$pass,
                'prefix' => self::$tbprefix,
                'charset' => self::$encoding
            ]);
            $db->query("CREATE TABLE IF NOT EXISTS " . self::$tbprefix . "admins (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, privilegi int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . self::$tbprefix . "cookies (id int(100) NOT NULL auto_increment, uid int(100) NOT NULL, uuid varchar(255) COLLATE utf8_unicode_ci NOT NULL, type varchar(10) COLLATE utf8_unicode_ci NOT NULL, auth_token varchar(255) COLLATE utf8_unicode_ci NOT NULL, lastseen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY uuid (uuid)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
			$login = new \login\Controllers\Login;
			$data = $db->insert("admins", [
                "user" => $user,
                "pass" => $login->create_hash($pass),
                "email" => $email,
                "superuser" => $login->encrypt($user, $salt),
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