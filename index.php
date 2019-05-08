<?php

/*############################################
                 PerSeo CMS

        Copyright © 2018 BrainStorm
        https://www.per-seo.com

*/############################################

error_reporting(E_ERROR | E_PARSE);
try {
    @include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'version.php');
    if ((!@include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) || (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))) {
        throw new \Exception (__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php does not exist. Use composer to install dependencies.');
    }
    @include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
    if (defined('CRYPT_SALT')) {
        ini_set('session.save_handler', 'files');
        $key = CRYPT_SALT;
        $handler = new \PerSeo\Sessions($key);
        session_set_save_handler($handler, true);
    }
    if (ob_get_length()) {
        ob_end_clean();
    }
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))) {
        ob_start('ob_gzhandler');
    } else {
        ob_start();
    }
    session_start();
    if ((!@include_once(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php')) || (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'))) {
        throw new \Exception (__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php does not exist. Download default one from repository.');
    }
} catch (Exception $e) {
    die("PerSeo ERROR : " . $e->getMessage());
}