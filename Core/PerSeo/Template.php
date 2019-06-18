<?php

namespace PerSeo;

class Template
{
    public static function vars($container)
    {
        $vars = array();
        $vars['ProdName'] = $container->get('settings.prodname');
        $vars['ProdVer'] = $container->get('settings.prodver');
        $vars['encoding'] = $container->has('settings.global') ? $container->get('settings.global')['encoding'] : '';
        $vars['language'] = $container->get('current.language');
        $vars['ModuleName'] = \PerSeo\Path::ModuleName();
        $vars['ModuleUrlName'] = strtolower(\PerSeo\Path::ModuleName());
        return $vars;
    }
}