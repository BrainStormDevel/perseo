<?php

namespace Modules\wizard\Classes;

use Exception;
use PerSeo\DB;

class InstallPGSQLDB
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
                'type' => $driver,
				'host' => $dbhost,
                'database' => $dbname,
                'username' => $dbuser,
                'password' => $dbpass,
                'prefix' => $prefix,
                'charset' => $charset,
				'port' => $dbport,
				'error' => \PDO::ERRMODE_EXCEPTION
            ]);
			$db->create("admins", [
				"id" => [
					"SERIAL",
					"PRIMARY KEY"
				],
				"user" => [
					"VARCHAR(100)",
					"NOT NULL",
					"UNIQUE"
				],
				"pass" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"email" => [
					"VARCHAR(255)",
					"NOT NULL",
					"UNIQUE"
				],
				"superuser" => [
					"VARCHAR(255)",
					"NULL"
				],
				"type" => [
					"SMALLINT",
					"NULL"
				],
				"stato" => [
					"SMALLINT",
					"NOT NULL",
					"DEFAULT",
					1
				]
			]);
			$db->create("cookies", [
				"id" => [
					"SERIAL",
					"PRIMARY KEY"
				],
				"uid" => [
					"INTEGER",
					"NOT NULL"
				],
				"uuid" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"type" => [
					"VARCHAR(10)",
					"NOT NULL"
				],
				"auth_token" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"lastseen" => [
					"TIMESTAMP",
					"NOT NULL",
					"DEFAULT",
					"CURRENT_TIMESTAMP"
				]
			]);
			$db->create("routes", [
				"id" => [
					"SERIAL",
					"PRIMARY KEY"
				],
				"request" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"dest" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"type" => [
					"SMALLINT",
					"NOT NULL",
					"DEFAULT",
					1
				],
				"redirect" => [
					"INTEGER",
					"NOT NULL",
					"DEFAULT",
					301
				],
				"canonical" => [
					"SMALLINT",
					"NOT NULL",
					"DEFAULT",
					0
				]
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
		return json_encode($result);
	}
}