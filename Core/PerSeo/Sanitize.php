<?php

namespace PerSeo;

class Sanitize
{
    protected static $MAX_A = 200;

    protected static $MAX_U = 16;

    protected static $MAX_P = 20;

    protected static $MAX_E = 40;

    protected static $MAX_T = 100;

    public static function alpha($string)
    {
        $pattern = '/[^A-Za-z0-9]/';
        return trim(substr(preg_replace($pattern, '', $string), 0, self::$MAX_A));
    }

    public static function user($string)
    {
        $pattern = '/[^A-Za-z0-9-_.]/';
        return trim(substr(preg_replace($pattern, '', $string), 0, self::$MAX_U));
    }

    public static function pwd($string)
    {
        $pattern = '/[^A-Za-z0-9-#_$^&@%,.]/';
        return trim(substr(preg_replace($pattern, '', $string), 0, self::$MAX_P));
    }

    public static function email($string)
    {
        return substr(filter_var($string, FILTER_SANITIZE_EMAIL), 0, self::$MAX_E);
    }

    public static function no_xss($string)
    {
        $pattern = strip_tags($string, '<p><table><tbody><thead><tr><td>');
        return trim(self::xssinject($pattern));
    }

    private static function xssinject($string)
    {
        $pattern = '/\sseeksegmenttime=|\sonmousedown=|\sonmousemove=|\sonmmouseup=|\sonmouseover=|\sonmouseout=|\sonload=|\sonunload=|\sonfocus=|\sonblur=|\sonchange=|\sonsubmit=|\sondblclick=|\sonclick=|\sonkeydown=|\sonkeyup=|\sonkeypress=|\sonmouseenter=|\sonmouseleave=|\sonerror=|\sonselect=|\sonreset=|\sonabort=|\sondragdrop=|\sonresize=|\sonactivate=|\sonafterprint=|\sonmoveend=|\sonafterupdate=|\sonbeforeactivate=|\sonbeforecopy=|\sonbeforecut=|\sonbeforedeactivate=|\sonbeforeeditfocus=|\sonbeforepaste=|\sonbeforeprint=|\sonbeforeunload=|\sonbeforeupdate=|\sonmove=|\sonbounce=|\soncellchange=|\soncontextmenu=|\soncontrolselect=|\soncopy=|\soncut=|\sondataavailable=|\sondatasetchanged=|\sondatasetcomplete=|\sondeactivate=|\sondrag=|\sondragend=|\sondragenter=|\sonmousewheel=|\sondragleave=|\sondragover=|\sondragstart=|\sondrop=|\sonerrorupdate=|\sonfilterchange=|\sonfinish=|\sonfocusin=|\sonfocusout=|\sonhashchange=|\sonhelp=|\soninput=|\sonlosecapture=|\sonmessage=|\sonmouseup=|\sonmovestart=|\sonoffline=|\sononline=|\sonpaste=|\sonpropertychange=|\sonreadystatechange=|\sonresizeend=|\sonresizestart=|\sonrowenter=|\sonrowexit=|\sonrowsdelete=|\sonrowsinserted=|\sonscroll=|\sonsearch=|\sonselectionchange=|\sonselectstart=|\sonstart=|\sonstop=|\sformaction=|\sonforminput=|\sonformchange=|xlink:href=|\sdirname=|\ssrcdoc=|\ssrcset=|\sbackground=|javascript:/i';
        return trim(preg_replace($pattern, ' ', $string));
    }

    public static function no_html($string)
    {
        $pattern = filter_var($string, FILTER_SANITIZE_STRING);
        return trim(self::xssinject($pattern));
    }

    public static function to_url($string)
    {
        $string = self::unaccent($string);
        $string = preg_replace('/\s/i', '-', $string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9-]/', '', $string);
        return trim(substr($string, 0, self::$MAX_T));
    }

    public static function unaccent($txt)
    {
        $transliterationTable = array(
            'á' => 'a',
            'Á' => 'A',
            'à' => 'a',
            'À' => 'A',
            'ă' => 'a',
            'Ă' => 'A',
            'â' => 'a',
            'Â' => 'A',
            'å' => 'a',
            'Å' => 'A',
            'ã' => 'a',
            'Ã' => 'A',
            'ą' => 'a',
            'Ą' => 'A',
            'ā' => 'a',
            'Ā' => 'A',
            'ä' => 'ae',
            'Ä' => 'AE',
            'æ' => 'ae',
            'Æ' => 'AE',
            'ḃ' => 'b',
            'Ḃ' => 'B',
            'ć' => 'c',
            'Ć' => 'C',
            'ĉ' => 'c',
            'Ĉ' => 'C',
            'č' => 'c',
            'Č' => 'C',
            'ċ' => 'c',
            'Ċ' => 'C',
            'ç' => 'c',
            'Ç' => 'C',
            'ď' => 'd',
            'Ď' => 'D',
            'ḋ' => 'd',
            'Ḋ' => 'D',
            'đ' => 'd',
            'Đ' => 'D',
            'ð' => 'dh',
            'Ð' => 'Dh',
            'é' => 'e',
            'É' => 'E',
            'è' => 'e',
            'È' => 'E',
            'ĕ' => 'e',
            'Ĕ' => 'E',
            'ê' => 'e',
            'Ê' => 'E',
            'ě' => 'e',
            'Ě' => 'E',
            'ë' => 'e',
            'Ë' => 'E',
            'ė' => 'e',
            'Ė' => 'E',
            'ę' => 'e',
            'Ę' => 'E',
            'ē' => 'e',
            'Ē' => 'E',
            'ḟ' => 'f',
            'Ḟ' => 'F',
            'ƒ' => 'f',
            'Ƒ' => 'F',
            'ğ' => 'g',
            'Ğ' => 'G',
            'ĝ' => 'g',
            'Ĝ' => 'G',
            'ġ' => 'g',
            'Ġ' => 'G',
            'ģ' => 'g',
            'Ģ' => 'G',
            'ĥ' => 'h',
            'Ĥ' => 'H',
            'ħ' => 'h',
            'Ħ' => 'H',
            'í' => 'i',
            'Í' => 'I',
            'ì' => 'i',
            'Ì' => 'I',
            'î' => 'i',
            'Î' => 'I',
            'ï' => 'i',
            'Ï' => 'I',
            'ĩ' => 'i',
            'Ĩ' => 'I',
            'į' => 'i',
            'Į' => 'I',
            'ī' => 'i',
            'Ī' => 'I',
            'ĵ' => 'j',
            'Ĵ' => 'J',
            'ķ' => 'k',
            'Ķ' => 'K',
            'ĺ' => 'l',
            'Ĺ' => 'L',
            'ľ' => 'l',
            'Ľ' => 'L',
            'ļ' => 'l',
            'Ļ' => 'L',
            'ł' => 'l',
            'Ł' => 'L',
            'ṁ' => 'm',
            'Ṁ' => 'M',
            'ń' => 'n',
            'Ń' => 'N',
            'ň' => 'n',
            'Ň' => 'N',
            'ñ' => 'n',
            'Ñ' => 'N',
            'ņ' => 'n',
            'Ņ' => 'N',
            'ó' => 'o',
            'Ó' => 'O',
            'ò' => 'o',
            'Ò' => 'O',
            'ô' => 'o',
            'Ô' => 'O',
            'ő' => 'o',
            'Ő' => 'O',
            'õ' => 'o',
            'Õ' => 'O',
            'ø' => 'oe',
            'Ø' => 'OE',
            'ō' => 'o',
            'Ō' => 'O',
            'ơ' => 'o',
            'Ơ' => 'O',
            'ö' => 'oe',
            'Ö' => 'OE',
            'ṗ' => 'p',
            'Ṗ' => 'P',
            'ŕ' => 'r',
            'Ŕ' => 'R',
            'ř' => 'r',
            'Ř' => 'R',
            'ŗ' => 'r',
            'Ŗ' => 'R',
            'ś' => 's',
            'Ś' => 'S',
            'ŝ' => 's',
            'Ŝ' => 'S',
            'š' => 's',
            'Š' => 'S',
            'ṡ' => 's',
            'Ṡ' => 'S',
            'ş' => 's',
            'Ş' => 'S',
            'ș' => 's',
            'Ș' => 'S',
            'ß' => 'SS',
            'ť' => 't',
            'Ť' => 'T',
            'ṫ' => 't',
            'Ṫ' => 'T',
            'ţ' => 't',
            'Ţ' => 'T',
            'ț' => 't',
            'Ț' => 'T',
            'ŧ' => 't',
            'Ŧ' => 'T',
            'ú' => 'u',
            'Ú' => 'U',
            'ù' => 'u',
            'Ù' => 'U',
            'ŭ' => 'u',
            'Ŭ' => 'U',
            'û' => 'u',
            'Û' => 'U',
            'ů' => 'u',
            'Ů' => 'U',
            'ű' => 'u',
            'Ű' => 'U',
            'ũ' => 'u',
            'Ũ' => 'U',
            'ų' => 'u',
            'Ų' => 'U',
            'ū' => 'u',
            'Ū' => 'U',
            'ư' => 'u',
            'Ư' => 'U',
            'ü' => 'ue',
            'Ü' => 'UE',
            'ẃ' => 'w',
            'Ẃ' => 'W',
            'ẁ' => 'w',
            'Ẁ' => 'W',
            'ŵ' => 'w',
            'Ŵ' => 'W',
            'ẅ' => 'w',
            'Ẅ' => 'W',
            'ý' => 'y',
            'Ý' => 'Y',
            'ỳ' => 'y',
            'Ỳ' => 'Y',
            'ŷ' => 'y',
            'Ŷ' => 'Y',
            'ÿ' => 'y',
            'Ÿ' => 'Y',
            'ź' => 'z',
            'Ź' => 'Z',
            'ž' => 'z',
            'Ž' => 'Z',
            'ż' => 'z',
            'Ż' => 'Z',
            'þ' => 'th',
            'Þ' => 'Th',
            'µ' => 'u',
            'а' => 'a',
            'А' => 'a',
            'б' => 'b',
            'Б' => 'b',
            'в' => 'v',
            'В' => 'v',
            'г' => 'g',
            'Г' => 'g',
            'д' => 'd',
            'Д' => 'd',
            'е' => 'e',
            'Е' => 'E',
            'ё' => 'e',
            'Ё' => 'E',
            'ж' => 'zh',
            'Ж' => 'zh',
            'з' => 'z',
            'З' => 'z',
            'и' => 'i',
            'И' => 'i',
            'й' => 'j',
            'Й' => 'j',
            'к' => 'k',
            'К' => 'k',
            'л' => 'l',
            'Л' => 'l',
            'м' => 'm',
            'М' => 'm',
            'н' => 'n',
            'Н' => 'n',
            'о' => 'o',
            'О' => 'o',
            'п' => 'p',
            'П' => 'p',
            'р' => 'r',
            'Р' => 'r',
            'с' => 's',
            'С' => 's',
            'т' => 't',
            'Т' => 't',
            'у' => 'u',
            'У' => 'u',
            'ф' => 'f',
            'Ф' => 'f',
            'х' => 'h',
            'Х' => 'h',
            'ц' => 'c',
            'Ц' => 'c',
            'ч' => 'ch',
            'Ч' => 'ch',
            'ш' => 'sh',
            'Ш' => 'sh',
            'щ' => 'sch',
            'Щ' => 'sch',
            'ъ' => '',
            'Ъ' => '',
            'ы' => 'y',
            'Ы' => 'y',
            'ь' => '',
            'Ь' => '',
            'э' => 'e',
            'Э' => 'e',
            'ю' => 'ju',
            'Ю' => 'ju',
            'я' => 'ja',
            'Я' => 'ja'
        );
        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
    }
    /*private static function unaccent($stringa) {
        if (strpos($stringa = htmlentities($stringa, ENT_QUOTES, 'UTF-8'), '&') !== false) {
            $stringa = trim(html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $stringa), ENT_QUOTES, 'UTF-8'));
        }
        return $stringa;
    }*/
}