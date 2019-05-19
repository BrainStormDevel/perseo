<?php

namespace wizard\Controllers;

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

    public static function main($container)
    {
        $fileconf = \PerSeo\Path::CONF_PATH . \PerSeo\Path::DS . 'config.php';
        try {
            $myfile = fopen($fileconf, "w");
            $content = "<?php\n";
            $content .= "define('DBHOST', '" . $container->get('Sanitizer')->POST('dbhost', 'user') . "');\n";
            $content .= "define('DB', 'mysql');\n";
            $content .= "define('DBPATH', NULL);\n";
            $content .= "define('DBNAME', '" . $container->get('Sanitizer')->POST('dbname', 'pass') . "');\n";
            $content .= "define('DBUSER', '" . $container->get('Sanitizer')->POST('dbuser', 'user') . "');\n";
            $content .= "define('DBPASS', '" . $container->get('Sanitizer')->POST('dbpass', 'pass') . "');\n";
            $content .= "define('DBENCODING', '" . $container->get('Sanitizer')->POST('dbencoding') . "');\n";
            $content .= "define('SITENAME', '" . $container->get('Sanitizer')->POST('title') . "');\n";
            $content .= "define('POSTFIX', '.html');\n";
            $content .= "define('ENCODING', '" . $container->get('Sanitizer')->POST('encoding') . "');\n";
            $content .= "define('LANG_DEFAULT', '" . $container->get('Sanitizer')->POST('lang', 'alpha') . "');\n";
            $content .= "define('ADM_COOKNAME', '" . $container->get('Sanitizer')->POST('cookadm', 'user') . "');\n";
            $content .= "define('USR_COOKNAME', '" . $container->get('Sanitizer')->POST('cookusr', 'user') . "');\n";
            $content .= "define('MAX_U', '16');\n";
            $content .= "define('MAX_P', '20');\n";
            $content .= "define('MAX_E', '40');\n";
            $content .= "define('MAX_T', '100');\n";
            $content .= "define('MAX_FLOOD', '1');\n";
            $content .= "define('CRYPT_SALT', '" . $container->get('Sanitizer')->POST('salt', 'alpha') . "');\n";
            $content .= "define('TBL_', '" . $container->get('Sanitizer')->POST('prefix', 'user') . "');\n";
            $content .= "define('COOKIE_EXPIRE', '" . $container->get('Sanitizer')->POST('cookexp', 'alpha') . "');\n";
            $content .= "define('COOKIE_MAX_EXPIRE', '" . $container->get('Sanitizer')->POST('cookmaxexp', 'alpha') . "');\n";
            $content .= "define('COOKIE_PATH', '" . $container->get('Sanitizer')->POST('cookpath') . "');\n";
            $content .= "define('COOKIE_SECURE', false);\n";
            $content .= "define('COOKIE_HTTP', true);\n";
            if (!empty($container->get('Sanitizer')->POST('facebook_app')) && !empty($container->get('Sanitizer')->POST('facebook_secret'))) {
                $faceapp = 'F_APP_' . $_SERVER['SERVER_NAME'];
                $facesecret = 'F_SECRET_' . $_SERVER['SERVER_NAME'];
                $content .= "define('" . $faceapp . "', '" . $container->get('Sanitizer')->POST('facebook_app') . "');\n";
                $content .= "define('" . $facesecret . "', '" . $container->get('Sanitizer')->POST('facebook_secret') . "');\n";
            }
            if (!empty($container->get('Sanitizer')->POST('google_key')) && !empty($container->get('Sanitizer')->POST('google_secret'))) {
                $googlekey = 'G_KEY_' . $_SERVER['SERVER_NAME'];
                $googlesecret = 'G_SECRET_' . $_SERVER['SERVER_NAME'];
                $content .= "define('" . $googlekey . "', '" . $container->get('Sanitizer')->POST('google_key') . "');\n";
                $content .= "define('" . $googlesecret . "', '" . $container->get('Sanitizer')->POST('google_secret') . "');\n";
            }
            fwrite($myfile, $content);
			fclose($myfile);
            self::$host = $container->get('Sanitizer')->POST('dbhost', 'user');
            self::$name = $container->get('Sanitizer')->POST('dbname', 'pass');
            self::$user = $container->get('Sanitizer')->POST('dbuser', 'user');
            self::$pass = $container->get('Sanitizer')->POST('dbpass', 'pass');
            self::$encoding = $container->get('Sanitizer')->POST('dbencoding');
            self::$port = '3306';
            self::$tbprefix = $container->get('Sanitizer')->POST('prefix', 'user');
            self::$salt = $container->get('Sanitizer')->POST('salt', 'alpha');
            $result = self::createdb($container->get('Sanitizer')->POST('admin', 'user'),
            $container->get('Sanitizer')->POST('email', 'email'), $container->get('Sanitizer')->POST('password', 'pass'),
            $container->get('Sanitizer')->POST('salt', 'alpha'));
            session_unset();
            session_destroy();
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
            $db = new \PerSeo\DB('mysql', self::$name, self::$host, self::$user, self::$pass, self::$tbprefix,
                self::$encoding);
            $db->query("CREATE TABLE IF NOT EXISTS " . self::$tbprefix . "admins (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, privilegi int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . self::$tbprefix . "cookies (id int(100) NOT NULL auto_increment, uid int(100) NOT NULL, uuid varchar(255) COLLATE utf8_unicode_ci NOT NULL, type varchar(10) COLLATE utf8_unicode_ci NOT NULL, auth_token varchar(255) COLLATE utf8_unicode_ci NOT NULL, lastseen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY uuid (uuid)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
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