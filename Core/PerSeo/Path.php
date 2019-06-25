<?php

namespace PerSeo;

define('DS', DIRECTORY_SEPARATOR);
define('D_ROOT', realpath(__DIR__ . DS . '..' . DS . '..'));

class Path
{
    const DS = DS;
    const D_ROOT = D_ROOT;
    const CORE_PATH = D_ROOT . DS . 'Core';
    const CONF_PATH = D_ROOT . DS . 'config';
    const MOD_PATH = D_ROOT . DS . 'modules';

    public static function LangPath($module)
    {
        return realpath(self::MOD_PATH . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'languages');
    }

    public static function LangAdminPath($module)
    {
        return realpath(self::MOD_PATH . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'admin');
    }

    public static function SiteName($request)
    {
        return '//' . $_SERVER['HTTP_HOST'] . $request->getUri()->getBasePath();
    }

    public static function cookiepath($request)
    {
        return ($request->getUri()->getBasePath() == '/' ? $request->getUri()->getBasePath() : $request->getUri()->getBasePath() . '/');
    }
}