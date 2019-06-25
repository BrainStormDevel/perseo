<?php

namespace PerSeo;

class Template
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function vars($modulename)
    {
        $vars = array();
        $vars['ProdName'] = $this->container->get('settings.prodname');
        $vars['ProdVer'] = $this->container->get('settings.prodver');
        $vars['encoding'] = $this->container->has('settings.global') ? $this->container->get('settings.global')['encoding'] : '';
        $vars['template'] = $this->container->has('settings.global') ? $this->container->get('settings.global')['template'] : '';
        $vars['language'] = $this->container->get('current.language');
        $vars['ModuleName'] = $modulename;
        $vars['ModuleUrlName'] = strtolower($modulename);
        return $vars;
    }
}