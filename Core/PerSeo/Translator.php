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
    }

    public function __($str)
    {
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists($this->path . $this->language . '.lng')) {
                $strings = array_map(array($this, 'splitStrings'), file($this->path . $this->language . '.lng'));
                foreach ($strings as $k => $v) {
                    $this->lang[$this->language][$v[0]] = $v[1];
                }
                return $this->findString($str);
            } else {
                return $str;
            }
        } else {
            return $this->findString($str);
        }
    }

    private function findString($str)
    {
        if (array_key_exists($str, $this->lang[$this->language])) {
            return $this->lang[$this->language][$str];
        }
        return $str;
    }

    public function module($name)
    {
        $start = '<' . $name . '>';
        $end = '</' . $name . '>';
        $startpos = false;
        $endpos = false;
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists($this->path . $this->language . '.lng')) {
                $str = array();
                $line = array();
                $handle = fopen($this->path . $this->language . '.lng', 'r');
                while (($buffer = fgets($handle)) !== false) {
                    if (strpos($buffer, $start) !== false) {
                        $startpos = true;
                    }
                    if (strpos($buffer, $end) !== false) {
                        $endpos = true;
                    }
                    if ($startpos) {
                        $line = $this->splitStrings($buffer);
                        if (isset($line[1]) && ($line[1] != null)) {
                            $str[$line[0]] = $line[1];
                        }
                    }
                    if ($startpos && $endpos) {
                        break;
                    }
                }
                fclose($handle);
                $this->result = array_merge($this->result, $str);
            }
        }
    }

    private function splitStrings($str)
    {
        return explode('=', trim($str));
    }

    public function vars()
    {
        return $this->result;
    }
}