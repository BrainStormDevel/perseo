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
			"[>]admins_priv" => ["privilegi" => "pid"]
		], [		
			"admins.id (id)",
			"admins.user (user)",
			"admins.email (email)",
			"admins.privilegi (privilegi)",
			"admins.stato (stato)",
			"admins_priv.label (label)"
		]);
		return $result;
    }
}