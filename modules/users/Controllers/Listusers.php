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
			"id",
			"user",
			"email",
			"privilegi",
			"stato"
		]);
		return $result;
    }
}