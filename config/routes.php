<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $thiscontainer = $app->getContainer();
    $directory = $thiscontainer->get('settings.modules');
    $dirobj = new \DirectoryIterator($directory);
    $modules = array();
    $curmod = 0;
    foreach ($dirobj as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $menu = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'menu.json';
            $routes = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'routes.php';
            $modules[$curmod]['name'] = $fileinfo->getBasename();
            if (file_exists($menu)) {
                $currfile = file_get_contents($menu);
                $modules[$curmod]['menu'] = json_decode($currfile, true);
            }
            if (file_exists($routes)) {
                @include_once($routes);
            }
            $curmod++;
        }
    }
};
