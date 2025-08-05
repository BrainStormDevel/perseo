<?php

namespace Modules\wizard\Classes;

use Exception;
use PerSeo\DB;

class InstallSQLDB
{
	protected string $fileconf;
	
	public function __construct(string $fileconf)
    {
		$this->fileconf = $fileconf;
    }
		
	public function createDB(string $driver, string $dbhost, string $dbname, string $dbuser, string $dbpass, string $prefix, string $charset, int $dbport = 3306): string {
		try {
			$configfile = (file_exists($this->fileconf) ? $this->fileconf : '');  
			$content = file_get_contents($configfile);
			$dbSettings = <<<PHP
			],
				'settings_db' => [
					'default' => [
					'type' => '$driver',
					'host' => '$dbhost',
					'database' => '$dbname',
					'username' => '$dbuser',
					'password' => '$dbpass',
					'prefix' => '$prefix',
					'charset' => '$charset',
					'port' => $dbport
					]\n\t
			PHP;
			if (preg_match_all('/\]\s*\];/s', $content, $matches, PREG_OFFSET_CAPTURE)) {
				$pos = end(end($matches[0]));
				$newContent = substr_replace($content, $dbSettings, $pos, 0);
				file_put_contents($configfile, $newContent);
			} else {
				throw new Exception("Error edit config file", 3);
			}
            $db = new DB([
                'database_type' => $driver,
                'database_name' => $dbname,
                'server' => $dbhost,
                'username' => $dbuser,
                'password' => $dbpass,
                'prefix' => $prefix,
                'charset' => $charset
            ]);
            $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "admins (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, type int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "cookies (id int(100) NOT NULL auto_increment, uid int(100) NOT NULL, uuid varchar(255) COLLATE utf8_unicode_ci NOT NULL, type varchar(10) COLLATE utf8_unicode_ci NOT NULL, auth_token varchar(255) COLLATE utf8_unicode_ci NOT NULL, lastseen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY uuid (uuid)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "admins_types (id int(100) NOT NULL auto_increment, pid int(100) NOT NULL, label varchar(100) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (id), UNIQUE KEY pid (pid)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "users (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, type int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "routes (id int(100) NOT NULL auto_increment, request varchar(255) COLLATE utf8_unicode_ci NOT NULL, dest varchar(255) COLLATE utf8_unicode_ci NOT NULL, type int(2) NOT NULL DEFAULT 1, redirect int(3) NOT NULL DEFAULT 301, canonical int(2) NOT NULL DEFAULT 0, PRIMARY KEY (id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
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
		return json_encode($result);
	}
}