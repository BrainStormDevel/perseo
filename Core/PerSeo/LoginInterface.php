<?php

namespace PerSeo;

interface LoginInterface
{
	public function login($username, $password, $type, $remember = NULL);
}