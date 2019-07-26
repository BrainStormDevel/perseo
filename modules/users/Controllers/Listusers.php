<?php

namespace users\Controllers;

class Listusers
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function admins()
    {
		$db = $this->container->get('db');
		$result = $db->select("admins", [
			"[>]admins_types" => ["type" => "pid"]
		], [		
			"admins.id (id)",
			"admins.user (user)",
			"admins.email (email)",
			"admins.type (type)",
			"admins.stato (stato)",
			"admins_types.label (label)"
		]);
		return $result;
    }
    public function adminstype()
    {
		$db = $this->container->get('db');
		$result = $db->select("admins_types", [	
			"pid",
			"label"
		]);
		return $result;
    }	
}