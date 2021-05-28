<?php

namespace Modules\wizard\Controllers;

use Exception;
use PerSeo\DB;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Install
{
	protected $container;
	protected $driver;
	protected $host;
	protected $name;
	protected $user;
	protected $pass;
	protected $encoding;
	protected $port;
	protected $tbprefix;
	protected $salt;
	protected $admin; 
	protected $email; 
	protected $password;

    public function __construct(ContainerInterface $container)
    {
		$this->container = $container;
    }

    public function __invoke(Request $request, Response $response): Response
	{
		$post = $request->getParsedBody();
		$config = $this->container->get('settings.root') .'/config';
        $fileconf = $config . DIRECTORY_SEPARATOR .'settings.php';
        try {
            $myfile = fopen($fileconf, "w");
            $content = "<?php\n\n";
            $content .= "return [
    'settings.global' => [
        'sitename' => '" . (string) $post['title'] . "',
        'encoding' => '" . (string) $post['encoding'] . "',
		'template' => '" . (string) $post['template'] . "',
		'locale' => " . (boolval($post['locale']) ? 'true' : 'false') . ",
		'maintenance' => false,
		'maintenancekey' => '" . (string) $post['maintenancekey'] . "',
        'language' => '" . (string) $post['defaultlang'] . "',
		'languages' => ['it', 'en']
    ],
    'settings.root' => realpath(__DIR__ .'/..'),
    'settings.temp' => realpath(__DIR__ .'/../tmp'),
	'settings.modules' =>  realpath(__DIR__ .'/../modules'),
    'settings.error' => [
        'display_error_details' => false,
		'log_errors' => true,
        'log_error_details' => true
    ],
	'settings.session' =>[
        'name' => 'client',
        'cache_expire' => 0,
	],
	'settings.twig' => [
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
	'settings.logger' => [
		'name' => 'app',
		'path' => realpath(__DIR__ .'/../logs'),
		'filename' => 'app.log',
		'level' => \Monolog\Logger::DEBUG,
		'file_permission' => 0775,
	],
    'settings.secure' => [
        'crypt_salt' => '" . (string) $post['salt'] . "'
    ],
    'settings.cookie' => [
		'admin' => '" . (string) $post['cookadm'] . "',
		'user' => '" . (string) $post['cookusr'] . "',
        'cookie_exp' => '" . (string) $post['cookexp'] . "',
        'cookie_max_exp' => '" . (string) $post['cookmaxexp'] . "',
        'cookie_path' => '" . (string) $post['cookpath'] . "',
        'cookie_secure' => false,
        'cookie_http' => true
    ],
    'settings.db' => [
        'default' => [
            'driver' => '" . (string) $post['driver'] . "',
            'host' => '" . (string) $post['dbhost'] . "',
            'database' => '" . (string) $post['dbname'] . "',
            'username' => '" . (string) $post['dbuser'] . "',
            'password' => '" . (string) $post['dbpass'] . "',
            'prefix' => '" . (string) $post['prefix'] . "',
            'charset' => '" . (string) $post['dbencoding'] . "',
            'port' => 3306

        ]
    ]
];";
            fwrite($myfile, $content);
            fclose($myfile);
            $this->driver = (string) $post['driver'];
            $this->host = (string) $post['dbhost'];
            $this->name = (string) $post['dbname'];
            $this->user = (string) $post['dbuser'];
            $this->pass = (string) $post['dbpass'];
            $this->encoding = (string) $post['dbencoding'];
            $this->port = '3306';
            $this->tbprefix = (string) $post['prefix'];
            $this->salt = (string) $post['salt'];
			$this->admin = (string) $post['admin'];
			$this->email = (string) $post['email'];
			$this->password = (string) $post['password'];
			
            $result = $this->createdb();
            //setcookie(session_name(), “”, time() - 31556926, “ / ”);
            session_destroy();
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        $response->getBody()->write(json_encode($result));
		return $response;
    }

    protected function createdb()
    {
        try {
            $db = new DB([
                'database_type' => $this->driver,
                'database_name' => $this->name,
                'server' => $this->host,
                'username' => $this->user,
                'password' => $this->pass,
                'prefix' => $this->tbprefix,
                'charset' => $this->encoding
            ]);
            $db->query("CREATE TABLE IF NOT EXISTS " . $this->tbprefix . "admins (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, type int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $this->tbprefix . "cookies (id int(100) NOT NULL auto_increment, uid int(100) NOT NULL, uuid varchar(255) COLLATE utf8_unicode_ci NOT NULL, type varchar(10) COLLATE utf8_unicode_ci NOT NULL, auth_token varchar(255) COLLATE utf8_unicode_ci NOT NULL, lastseen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY uuid (uuid)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $this->tbprefix . "admins_types (id int(100) NOT NULL auto_increment, pid int(100) NOT NULL, label varchar(100) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (id), UNIQUE KEY pid (pid)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $this->tbprefix . "users (id int(100) NOT NULL auto_increment, user varchar(100) COLLATE utf8_unicode_ci NOT NULL, pass varchar(255) COLLATE utf8_unicode_ci NOT NULL, email varchar(255) COLLATE utf8_unicode_ci NOT NULL, superuser varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, type int(2) UNSIGNED DEFAULT NULL, stato int(2) NOT NULL, PRIMARY KEY (id), UNIQUE KEY user (user), UNIQUE KEY email (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS " . $this->tbprefix . "routes (id int(100) NOT NULL auto_increment, request varchar(255) COLLATE utf8_unicode_ci NOT NULL, dest varchar(255) COLLATE utf8_unicode_ci NOT NULL, type int(2) NOT NULL DEFAULT 1, redirect int(3) NOT NULL DEFAULT 301, canonical int(2) NOT NULL DEFAULT 0, PRIMARY KEY (id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            /*$login = new \login\Controllers\Login($container, 'admins');
            $db->insert("admins_types", [
                [
                    "pid" => 1,
                    "label" => "Administrator"
                ],
                [
                    "pid" => 2,
                    "label" => "Operator"
                ]
            ]);
            $db->insert("admins", [
                "user" => $user,
                "pass" => $login->create_hash($pass),
                "email" => $email,
                "superuser" => $login->encrypt($user, $salt),
                "type" => '1',
                "stato" => '0'
            ]);*/
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
        return $result;
    }
}