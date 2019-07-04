<?php

namespace PerSeo;

use Medoo\Medoo;

class DB extends Medoo
{
	private static $instance = null;
	
	private $conn;
	
    public function __construct(array $args)
    {
        try {
            $this->conn = parent::__construct($args);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	public static function getInstance(array $args)
	{
		if (self::$instance == null)
		{
			self::$instance = new DB($args);
		}
		return self::$instance;
	}
	public function getConnection()
	{
		return $this->conn;
	}
}