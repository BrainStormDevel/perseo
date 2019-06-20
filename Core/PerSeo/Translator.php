<?php

namespace PerSeo;

class Translator
{

    private $language = 'it';
    private $lang = array();
    private $path = '';
    private $result = array();

    public function __construct($language, $path)
    {
        $this->language = strtolower($language);
        $this->path = $path . DIRECTORY_SEPARATOR;
        $this->result = array();
		if (file_exists($this->path . $this->language . '.lng')) {
			$content = file_get_contents($this->path . $this->language . '.lng');
			$this->result = json_decode($content, true);
        }
    }

    public function get()
    {
        return $this->result;
    }
}