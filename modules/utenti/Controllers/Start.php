<?php

namespace Prova\Controllers;

class Start
{
	public function main()
    {
		$client = new \GuzzleHttp\Client([
			\GuzzleHttp\RequestOptions::VERIFY => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
		]);
    }
}