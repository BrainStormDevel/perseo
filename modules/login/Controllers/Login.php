<?php

namespace login\Controllers;

class Login
{
    protected static $id = '';
    protected static $name = '';
    protected static $superuser = '';
    protected static $privileges = '';
	protected $container;
    protected $type;
	protected $db;
	
	public function __construct($db, $type)
    {
        //$this->container = $container;
        $this->type = $type;
		$this->db = $db;
    }


}