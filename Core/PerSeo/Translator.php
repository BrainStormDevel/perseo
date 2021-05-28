<?php

namespace PerSeo;

class Translator
{

    protected $language;
    protected $path;
    protected $result;

    public function __construct(string $language = 'en', string $path = '')
    {
        $this->language = strtolower($language);
        $this->path = $path . DIRECTORY_SEPARATOR .'languages'. DIRECTORY_SEPARATOR;
        $this->result = array();
        if (file_exists($this->path . $this->language . '.lng')) {
            $content = file_get_contents($this->path . $this->language . '.lng');
            $this->result = $content;
        }
    }

    public function get()
    {
        return json_decode($this->result, true);
    }
}