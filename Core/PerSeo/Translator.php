<?php

namespace PerSeo;

class Translator
{
    private $language = 'it';
    private $lang = [];
    private $path = '';
    private $result = [];

    public function __construct($language, $path)
    {
        $this->language = strtolower($language);
        $this->path = $path.DIRECTORY_SEPARATOR;
        $this->result = [];
        if (file_exists($this->path.$this->language.'.lng')) {
            $content = file_get_contents($this->path.$this->language.'.lng');
            $this->result = json_decode($content, true);
        }
    }

    public function get()
    {
        return $this->result;
    }
}
