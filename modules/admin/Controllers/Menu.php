<?php

namespace admin\Controllers;

class Menu
{
    public function listall()
    {
        $menus = array();
        $directory = \PerSeo\Path::MOD_PATH;

        $dirobj = new \DirectoryIterator($directory);
        foreach ($dirobj as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $menu = $fileinfo->getFilename() . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'admin/menu.twig';
                if (file_exists($fileinfo->getPath() . DIRECTORY_SEPARATOR . $menu)) {
                    array_push($menus, $menu);
                }
            }
        }
        return $menus;
    }
}