<?php

namespace PerSeo;

class NewApp extends \DI\Bridge\Slim\App implements \DI\Bridge\Slim\ContainerBuilder
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions(Path::CONF_PATH . Path::DS . 'settings.php');
    }
}