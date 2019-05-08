<?php

namespace PerSeo;

class CheckConfig
{
    public static function verify()
    {
        $fileconf = \PerSeo\Path::CONF_PATH . \PerSeo\Path::DS . 'config.php';
        return !file_exists($fileconf);
    }
}