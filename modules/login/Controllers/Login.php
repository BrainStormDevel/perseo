<?php

namespace login\Controllers;

class Login
{
    public static function check($container)
    {
        $mylogin = new \PerSeo\Login();
        $typesArray = array(
            'admins',
            'users'
        );
        $type = (in_array($container->get('Sanitizer')->POST('type', 'alpha'),
            $typesArray) ? $container->get('Sanitizer')->POST('type', 'alpha') : 'admins');
        echo $mylogin->login($container->get('Sanitizer')->POST('username', 'user'),
            $container->get('Sanitizer')->POST('password', 'pass'), $type,
            ($container->get('Sanitizer')->POST('rememberme', 'int') == 1 ? false : true));
    }
}