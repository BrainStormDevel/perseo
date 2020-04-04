<?php

namespace PerSeo;

class Shutdown
{
    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
        register_shutdown_function([$this, 'fatal_handler']);
    }

    public function fatal_handler()
    {
        $error = error_get_last();

        if ($error !== null) {
            if (($error['type'] === E_ERROR) || ($error['type'] === E_PARSE) || ($error['type'] === E_CORE_ERROR) || ($error['type'] === E_COMPILE_ERROR)) {
                $output = [
                    'uri'    => $_SERVER['REQUEST_URI'],
                    'code'   => (string) $error['type'],
                    'message'=> (string) $error['message'].' '.(string) $error['file'].' '.(string) $error['line'],
                ];
                $settings = $this->settings['settings.logger']['critical'];
                $formoutput = "%datetime% > %level_name% > %message% %context% %extra%\n";
                $formatter = new \Monolog\Formatter\LineFormatter($formoutput);
                $stream = new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
                $stream->setFormatter($formatter);
                $logger = new \Monolog\Logger($settings['name']);
                $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
                $logger->pushHandler($stream);
                $logger->critical(json_encode($output));
                if ($this->settings['settings.displayErrorDetails']) {
                    echo 'An unrecoverable error has occurred'.PHP_EOL;
                    echo 'Error code: '.(string) $error['type'].PHP_EOL;
                    echo 'Message: '.$error['message'].PHP_EOL;
                    echo 'File: '.$error['file'].PHP_EOL;
                    echo 'Line: '.(string) $error['line'].PHP_EOL;
                }
                exit($error['type']);
            }
        }
    }
}
