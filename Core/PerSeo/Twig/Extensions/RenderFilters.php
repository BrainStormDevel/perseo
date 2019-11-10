<?php

namespace PerSeo\Twig\Extensions;

class RenderFilters extends \Twig_Extension
{
    public function getName()
    {
       return 'RenderFilters';
    }
   
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_decode', array($this, 'JSONDecode')),
        );
    }
	
	/*public function getFunctions()
	{
		return array(
			//new \Twig_SimpleFunction('showError', array($this, 'showError'), array('is_safe' => array('html'))),
			new \Twig_SimpleFunction('DateFormat', array($this, 'DateFormat')),
		);
	}*/
   
   public function JSONDecode(string $string = NULL)
   {
       return json_decode($string);
   }
}