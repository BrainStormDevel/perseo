<?php

namespace Modules\wizard\Classes;

use Exception;
use PerSeo\DB;

class TestMySQLDB
{
	
    public function __invoke(array $params): string {
        try {
			if (!extension_loaded('pdo')) { throw new Exception('PDO extension not present',0002); }
            $db = new DB([
                'type' => (string) $params['driver'],
				'host' => (string) $params['dbhost'],
                'database' => (string) $params['dbname'],
                'username' => (string) $params['dbuser'],
                'password' => (string) $params['dbpass'],
                'charset' => (string) $params['charset'],
				'collation' => (string) $params['collation'],
				'port' => (int) ((isset($params['dbport']) && !empty($params['dbport'])) ? $params['dbport'] : 3306)
            ]);
            $info = $db->info();
			$version = (string) $info['version'];
			if(strpos($version, 'MariaDB') !== false){
				$explode = explode("-", $version);
				$version = $explode[0];
				if ($version < '10.0.5') {
                    throw new Exception('Minimum requirements: Mariadb 10.0.5',0001);
                }
			}
            else {
                if ($version < '8.0.0') {
                    throw new Exception('Minimum requirements: Mysql 8.0.0',0001);
                }
            }
            $result = array(
                "err" => 0,
                "code" => 0,
                "msg" => "ok"
            );
        } catch (Exception $e) {
            $result = array(
                "err" => 1,
                "code" => $e->getCode(),
                "msg" => $e->getMessage()
            );
        }
		return json_encode($result);	
    }
}