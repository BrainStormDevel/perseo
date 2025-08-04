<?php

namespace Modules\wizard\Classes;

use Exception;
use PerSeo\DB;

class TestSQLiteDB
{
	protected string $configpath;
	
	public function __construct(string $configpath)
    {
		$this->configpath = $configpath;
    }
	
    public function __invoke(array $params): string {
        try {
			if (!extension_loaded('pdo_sqlite')) { throw new Exception('SQLite extension not present',0002); }
			$paramfile = $this->sanitizeFile($params['dbfile']);
			$prefix = $params['prefix'];
			$dbfile = $this->configpath . DIRECTORY_SEPARATOR . $paramfile;
			if (!file_exists($dbfile)) {
				$result = json_decode($this->createdb($dbfile));
				if ($result->err > 0) { throw new Exception('Unable to open '. $dbfile .' db file',0001); }
			}
            $db = new DB([
                'type' => (string) $params['driver'],
                'database' => (string) $dbfile
            ]);
			$db->create($prefix ."test", [
				"id" => [
					"INTEGER",
					"PRIMARY KEY"
				]
			]);
			$db->drop("test");
			unset($db);
			unlink($dbfile);
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
	
	protected function createdb(string $dbfile): string {
		$result = array(
                "err" => 0,
                "code" => 0,
                "msg" => "ok"
        );
		if (!file_exists($dbfile)) {
            try {
                touch($dbfile);
                chmod($dbfile, 0644);
            } catch (Exception $e) {
				$result = array(
					"err" => 1,
					"code" => $e->getCode(),
					"msg" => $e->getMessage()
				);
            }
        }
		return json_encode($result);
	}
	protected function sanitizeFile(string $file): string {

		$decoded = rawurldecode($file);
		
		$normalized = str_replace('\\', '/', $decoded);
		
		$patterns = [
			// Standard directory traversal
			'/\.\.\//',  
			'/\.\.\\\\/',
		
			// Unicode / exotic variations
			'/\x2e\x2e[\x2f\x5c]/i',            // ../ or ..\
			'/\x{ff0e}\x{ff0e}[\x{ff0f}\x{ff3c}]/u', // Fullwidth .. + slash or backslash
			'/\x{2215}/u',                      // Unicode division slash
			'/\xef\xbc\x8f/',                   // Fullwidth solidus
			'/\xc0\xaf/',                       // Overlong UTF-8 /
		];
		$cleaned = preg_replace($patterns, '', $normalized);
		
		// 4. Remove null bytes and control characters
		$cleaned = preg_replace('/[\x00-\x1f\x7f]/', '', $cleaned);
		
		return trim($cleaned);
	}
}