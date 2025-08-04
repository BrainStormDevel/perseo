<?php

namespace Modules\wizard\Classes;

class Config
{
	protected string $fileconf;
	
	public function __construct(string $fileconf)
    {
		$this->fileconf = $fileconf;
    }
	
	public function base(array $params): string {
		try {
            $myfile = fopen($this->fileconf, "w");
            $content = "<?php\n\n";
            $content .= "return [
	'settings_global' => [
		'sitename' => '" . (string) $params['title'] . "',
		'encoding' => '" . (string) $params['encoding'] . "',
		'template' => '" . (string) $params['template'] . "',
		'locale' => " . (boolval($params['locale']) ? 'true' : 'false') . ",
		'maintenance' => false,
		'maintenancekey' => '" . (string) $params['maintenancekey'] . "',
		'language' => '" . (string) $params['defaultlang'] . "',
		'languages' => ['it', 'en']
	],
	'settings_root' => realpath(__DIR__ .'/..'),
	'settings_temp' => realpath(__DIR__ .'/../tmp'),
	'settings_modules' =>  realpath(__DIR__ .'/../modules'),
	'settings_error' => [
		'reporting' => ['E_ALL', '~E_NOTICE'],
		'display_error_details' => false,
		'log_errors' => true,
		'log_error_details' => true
	],
	'settings_session' =>[
		'name' => '". substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10) ."',
		'cache_expire' => 0,
	],
	'settings_twig' => [
		// Template paths
		'paths' => [
			realpath(__DIR__ .'/../templates'),
		],
		'debug' => true,
		'path' => realpath(__DIR__ .'/../cache'),
		'url_base_path' => 'cache/',
		// Cache settings
		'cache_enabled' => false,
		'cache_path' => realpath(__DIR__ .'/../tmp'),
		'cache_name' => 'assets-cache',
		//  Should be set to 1 (enabled) in production
		'minify' => 0,
	],	
	'settings_logger' => [
		'name' => 'app',
		'path' => realpath(__DIR__ .'/../logs'),
		'filename' => 'app.log',
		'level' => \Monolog\Logger::DEBUG,
		'file_permission' => 0775,
	],
	'settings_secure' => [
		'crypt_salt' => '" . (string) $params['salt'] . "'
	],
	'settings_cookie' => [
		'admin' => '" . (string) $params['cookadm'] . "',
		'user' => '" . (string) $params['cookusr'] . "',
		'cookie_exp' => '" . (string) $params['cookexp'] . "',
		'cookie_max_exp' => '" . (string) $params['cookmaxexp'] . "',
		'cookie_path' => '" . (string) $params['cookpath'] . "',
		'cookie_secure' => false, //  Should be set to true in production
		'cookie_http' => true
	]
];";
			fwrite($myfile, $content);
            fclose($myfile);
			$result = array(
                'code' => '0',
                'msg' => 'OK'
            );
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
		return json_encode($result);
	}
}