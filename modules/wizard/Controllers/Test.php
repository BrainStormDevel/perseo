<?php

namespace wizard\Controllers;

class Test
{
    public static function main($container)
    {
        $db = new \PerSeo\DB("mysql", $container->get('Sanitizer')->POST('dbname', 'pass'),
            $container->get('Sanitizer')->POST('dbhost', 'user'), $container->get('Sanitizer')->POST('dbuser', 'user'),
            $container->get('Sanitizer')->POST('dbpass', 'pass'));
        $error = $db->err();
        if (isset($error['msg'])) {
            echo json_encode($error);
        } else {
            $result = Array(
                "err" => 0,
                "code" => 0,
                "msg" => "ok"
            );
            echo json_encode($result);
        }
    }

}