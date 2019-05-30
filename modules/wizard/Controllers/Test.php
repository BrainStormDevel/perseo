<?php

namespace wizard\Controllers;

class Test
{
    public static function main($container)
    {
        try {
            $db = new \PerSeo\DB([
                'database_type' => $container->get('Sanitizer')->POST('driver', 'user'),
                'database_name' => $container->get('Sanitizer')->POST('dbname', 'pass'),
                'server' => $container->get('Sanitizer')->POST('dbhost', 'user'),
                'username' => $container->get('Sanitizer')->POST('dbuser', 'user'),
                'password' => $container->get('Sanitizer')->POST('dbpass', 'pass'),
                'charset' => $container->get('Sanitizer')->POST('charset', 'user')
            ]);
            $result = Array(
                "err" => 0,
                "code" => 0,
                "msg" => "ok"
            );
        } catch (\Exception $e) {
            $result = Array(
                "err" => 1,
                "code" => $e->getCode(),
                "msg" => $e->getMessage()
            );
        }
        echo json_encode($result);
    }

}