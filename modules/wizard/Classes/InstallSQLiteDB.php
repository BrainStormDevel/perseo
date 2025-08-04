<?php

namespace Modules\wizard\Classes;

use Exception;
use PerSeo\DB;

class InstallSQLiteDB
{
	protected string $fileconf;
	protected string $path;
	
	public function __construct(string $fileconf, string $path)
    {
		$this->fileconf = $fileconf;
		$this->path = $path;
    }
		
	public function createDB(string $driver, string $dbfile, string $prefix = ''): string {
		try {
			$filedb = $this->path . DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR . $dbfile;
			$configfile = (file_exists($this->fileconf) ? $this->fileconf : '');  
			$content = file_get_contents($configfile);
			$dbSettings = <<<PHP
			],
				'settings_db' => [
					'default' => [
					'type' => '$driver',
					'database' => '$filedb',
					'prefix' => '$prefix'
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
                'database' => $filedb,
				'error' => \PDO::ERRMODE_EXCEPTION
            ]);
			$db->create($prefix ."admins", [
				"id" => [
					"INTEGER",
					"PRIMARY KEY"
				],
				"user" => [
					"VARCHAR(100)",
					"NOT NULL"
				],
				"pass" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"email" => [
					"VARCHAR(255)",
					"NOT NULL"
				],
				"superuser" => [
					"VARCHAR(255)",
					"NULL"
				],
				"type" => [
					"INTEGER(2)",
					"NULL"
				],
				"stato" => [
					"INTEGER(2)",
					"NOT NULL",
					"DEFAULT",
					1
				]
			]);
			$db->create($prefix ."cookies", [
				"id" => [
					"INTEGER",
					"PRIMARY KEY"
				],
				"uid" => [
					"INTEGER(100)",
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
					"DEFAULT",
					"CURRENT_TIMESTAMP"
				]
			]);
			$db->create($prefix ."admins_types", [
				"id" => [
					"INTEGER",
					"PRIMARY KEY"
				],
				"pid" => [
					"INTEGER(100)",
					"NOT NULL"
				],
				"label" => [
					"VARCHAR(100)",
					"NOT NULL"
				]
			]);
			$db->create($prefix ."routes", [
				"id" => [
					"INTEGER",
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
					"INTEGER(2)",
					"NOT NULL",
					"DEFAULT",
					1
				],
				"redirect" => [
					"INTEGER(3)",
					"NOT NULL",
					"DEFAULT",
					301
				],
				"canonical" => [
					"INTEGER(2)",
					"NOT NULL",
					"DEFAULT",
					0
				]
			]);
			unset($db);
			$fixht = $this->fixhtaccess($dbfile);
			$result = array(
                'code' => '0',
                'msg' => 'OK'
            );
        } catch (PDOException $e) {
			$result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
		}
		catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
		return json_encode($result);
	}
	protected function fixhtaccess($dbfile): string {
		try {
			$htaccessPath = $this->path . DIRECTORY_SEPARATOR .'.htaccess';
			$relativePath = "$dbfile";
			$insertBlock = <<<HTACCESS
			
			<Files "$relativePath">
				Require all denied
			</Files>
			HTACCESS;
			$contents = file_get_contents($htaccessPath);
			if (strpos($contents, '<Files "'. $relativePath .'">') === false) {
				$pattern = '/^(DirectoryIndex\s+index\.php\s*)$/mi';
				$modified = preg_replace($pattern, "$1" . $insertBlock, $contents, 1);
				file_put_contents($htaccessPath, $modified);
			}
			$result = array(
                'code' => '0',
                'msg' => 'OK'
            );
		}
		catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
		return json_encode($result);
	}
}