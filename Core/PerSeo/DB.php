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
    
    protected function buildRaw($raw, &$map)
	{
		if (!$this->isRaw($raw))
		{
			return false;
		}

		$query = preg_replace_callback(
			'/((FROM|JOIN|TABLE|INTO|UPDATE)\s*)?\<([a-zA-Z0-9_\.]+)\>/i',
			function ($matches)
			{
				if (!empty($matches[ 2 ]))
				{
					return $matches[ 2 ] . ' ' . $this->tableQuote($matches[ 3 ]);
				}

				return $this->columnQuote($matches[ 3 ]);
			},
			$raw->value);

		$raw_map = $raw->map;

		if (!empty($raw_map))
		{
			foreach ($raw_map as $key => $value)
			{
				$map[ $key ] = $this->typeMap($value, gettype($value));
			}
		}

		return $query;
	}

    public function isError()
    {
        $lastError = $this->error();

        return (isset($lastError[2]) && $lastError[2]) ? true : false;
    }
}
