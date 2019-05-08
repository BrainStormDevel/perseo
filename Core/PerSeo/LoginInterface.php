<?php

namespace PerSeo;

interface LoginInterface
{
    public static function create_hash($string);

    public static function validate_hash($string, $correct_hash);

    public static function encrypt($string, $key);

    public static function decrypt($string, $key);

    public function randStr($len);

    public function login($username, $password, $type, $remember);

    public function logout($type);

    public function islogged($type);

    public function _setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

    public function id();

    public function username();

    public function superuser();

    public function privileges();
}