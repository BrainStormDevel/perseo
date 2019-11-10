<?php

namespace PerSeo;

class Logger
{
	protected $logger;
	
    public function __construct($logger) {
		$this->logger = $logger;
    }
	public function error(string $message, int $code = 0) {
		$output = array(
			'code'=>$code,
			'message'=>$message
		);		
		$this->logger->error(json_encode($output));
    }
	public function warning(string $message, int $code = 0) {
		$output = array(
			'code'=>$code,
			'message'=>$message
		);		
		$this->logger->warning(json_encode($output));
    }
	public function notice(string $message, int $code = 0) {
		$output = array(
			'code'=>$code,
			'message'=>$message
		);		
		$this->logger->notice(json_encode($output));
    }
}