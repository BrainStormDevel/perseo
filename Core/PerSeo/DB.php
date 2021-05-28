<?php

namespace PerSeo;

use Medoo\Medoo;

class DB extends Medoo
{
    public function __construct(array $args)
    {
        try {
            parent::__construct($args);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
