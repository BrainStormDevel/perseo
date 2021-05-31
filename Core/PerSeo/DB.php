<?php

namespace PerSeo;

use Medoo\Medoo;

class DB extends Medoo
{
    public function __construct(array $args)
    {
        parent::__construct($args);
    }
}
