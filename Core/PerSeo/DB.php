<?php

namespace PerSeo;

use Medoo\Medoo;

class DB extends Medoo
{
    public function __construct(array $args)
    {
        try {
            parent::__construct($args);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }
	public function isError() {
		$lastError = $this->error();
        return (isset($lastError[2]) && $lastError[2]) ? true : false;
	}
}