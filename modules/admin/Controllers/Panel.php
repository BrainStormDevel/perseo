<?php

namespace admin\Controllers;

class Panel
{
	private $request;
	
	private $container;

    public function __construct($container, $request)
    {
        $this->container = $container;
		$this->request = $request;
    }
	public function get($body, $name = NULL, $header = NULL, $footer = NULL)
	{
		$csrfarray = array();
        $csrfarray['nameKey'] = $this->container->get('csrf')->getTokenNameKey();
        $csrfarray['valueKey'] = $this->container->get('csrf')->getTokenValueKey();
        $csrfarray['name'] = $this->request->getAttribute($csrfarray['nameKey']);
        $csrfarray['value'] = $this->request->getAttribute($csrfarray['valueKey']);
		if ($name != NULL) {
			$lang = new \PerSeo\Translator($this->container->get('current.language'), \PerSeo\Path::LangAdminPath($name));
			$langall = $lang->get();
		}
		$langadmin = new \PerSeo\Translator($this->container->get('current.language'), \PerSeo\Path::LangAdminPath('admin'));
		$langadminall = $langadmin->get();
		$langmenu = new \admin\Controllers\Langmenu($this->container);
		return [
			'menu' => $this->container->get('modules.name'),
			'langmenu' => $langmenu->get(),
			'csrf' => $csrfarray,
			'adminlang' => $langadminall['body'],
			'lang' => ($name != NULL ? $langall['body'] : array()),
			'titlesite' => $this->container->get('settings.global')['sitename'],
			'username' => \login\Controllers\Login::username(),
			'headertpl' => $header,			
			'bodytpl' => $body,
			'footertpl' => $footer,
			'host' => \PerSeo\Path::SiteName($this->request),
			'adm_host' => \PerSeo\Path::SiteName($this->request) . '/admin',
			'vars' => ($name != NULL ? $this->container->get('Templater')->vars($name) : $this->container->get('Templater')->vars('admin')),
			'cookiepath' => \PerSeo\Path::cookiepath($this->request)
		];
    }
}