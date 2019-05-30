<?php

namespace login\Controllers;

class Cookie
{
    public static function _setcookie($name, $value, $expire, $path, $domain, $secure = false, $httponly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}