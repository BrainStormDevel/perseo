<?php

namespace PerSeo;

class NewApp extends \DI\Bridge\Slim\App
{
    protected function configureContainer(\DI\ContainerBuilder $builder)
    {
        $builder->addDefinitions(\PerSeo\Path::CONF_PATH . \PerSeo\Path::DS . 'settings.php');
    }
}