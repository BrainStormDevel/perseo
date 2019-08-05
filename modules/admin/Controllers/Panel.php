<?php

namespace admin\Controllers;

class Panel
{
    private $request;

    private $container;

    private $vars = array();

    public function __construct($container, $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function add($name, $param)
    {
        $this->vars[$name] = $param;
    }

    public function get($body, $name = null, $header = null, $footer = null)
    {
        $csrfarray = array();
        $csrfarray['nameKey'] = $this->container->get('csrf')->getTokenNameKey();
        $csrfarray['valueKey'] = $this->container->get('csrf')->getTokenValueKey();
        $csrfarray['name'] = $this->request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $this->request->getAttribute($csrfarray['valueKey']);
        if ($name != null) {
            $lang = new \PerSeo\Translator($this->container->get('current.language'),
                \PerSeo\Path::LangAdminPath($name));
            $langall = $lang->get();
        }
        $langadmin = new \PerSeo\Translator($this->container->get('current.language'),
            \PerSeo\Path::LangAdminPath('admin'));
        $langadminall = $langadmin->get();
        $langmenu = new \admin\Controllers\Langmenu($this->container);
        $curvar = ($name != null ? $this->container->get('Templater')->vars($name) : $this->container->get('Templater')->vars('admin'));
        $vars = array_merge_recursive($curvar, $this->vars);
        return [
            'menu' => $this->container->get('modules.name'),
            'langmenu' => $langmenu->get(),
            'csrf' => $csrfarray,
            'adminlang' => $langadminall['body'],
            'lang' => ($name != null ? $langall['body'] : array()),
            'titlesite' => $this->container->get('settings.global')['sitename'],
            'username' => \login\Controllers\Login::user(),
            'headertpl' => $header,
            'bodytpl' => $body,
            'footertpl' => $footer,
            'host' => \PerSeo\Path::SiteName($this->request),
            'adm_host' => \PerSeo\Path::SiteName($this->request) . '/admin',
            'vars' => $vars,
            'mod' => (!empty($this->vars) ? $this->vars : array()),
            'cookiepath' => \PerSeo\Path::cookiepath($this->request)
        ];
    }
}