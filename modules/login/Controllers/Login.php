<?php

namespace login\Controllers;

class Login
{
    protected $type;
	protected $db;
	protected $cookie;
	protected $secure;
	
	private static $id;
	private static $user;
	private static $priv;
	private static $super;
	
	public function __construct($container, $type)
    {
        $this->type = $type;
		$this->db = ($container->has('db') ? $container->get('db') : NULL);
		$this->cookie = ($container->has('settings.cookie') ? $container->get('settings.cookie') : NULL);
		$this->secure = ($container->has('settings.secure') ? $container->get('settings.secure') : NULL);
    }

    public static function id()
    {
		return self::$id;
	}
	
    public static function user()
    {
		return self::$user;
	}
	
	public static function priv()
    {
		return self::$priv;
	}

    public static function superuser()
    {
		return self::$super;
	}
	
    public function encrypt($string, $key)
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext_base64 = base64_encode($iv . $hmac . $ciphertext_raw);
        return trim($ciphertext_base64);
    }

    public function check($user, $pass, $remember)
    {
        try {
            if ($this->islogged()) {
                throw new \Exception("OK", 0);
            }
            if (!$user or !$pass) {
                throw new \Exception("USR_PASS_EMPTY", 1);
            }
            $result = $this->db->select($this->type, [
                'id',
                'user',
                'pass',
				'superuser',
                'type'
            ], [
                'user' => $user
            ]);
            $error = $this->db->error();
            if (($error[1] != null) && ($error[2] != null)) {
                throw new \Exception($error[2], 1);
            }
            if (password_verify($pass, $result[0]['pass'])) {
				$logintype = $this->type .'_';
                $id = $result[0]['id'];
                if ($this->type == "admins") {
                    $cookname = $this->cookie['admin'];
                } else {
                    $cookname = $this->cookie['user'];
                }
                $cookiesalt = $this->randStr(100);
                $concat_string = $_SERVER['HTTP_USER_AGENT'] . ':~:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ':~:' . $cookiesalt;
                $token_first = base64_encode($concat_string);
                $tokenhash = $this->create_hash($token_first);
                $uid = substr(preg_replace("/[^A-Za-z0-9]/", '', $this->create_hash($id)), 11);
                $this->db->insert('cookies', [
                    "uid" => $id,
                    "uuid" => $uid,
                    "type" => $this->type,
                    "auth_token" => $tokenhash
                ]);
                $error = $this->db->error();
                if (($error[1] != null) && ($error[2] != null)) {
                    throw new \Exception($error[2], 1);
                }
                if ($remember) {
                    setcookie($cookname . '_COOKID', $uid,
                        time() + $this->cookie['cookie_max_exp'],
                        $this->cookie['cookie_path'], null,
                        $this->cookie['cookie_secure'],
                        $this->cookie['cookie_http']);
                    setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $this->cookie['cookie_max_exp'],
                        $this->cookie['cookie_path'], null,
                        $this->cookie['cookie_secure'],
                        $this->cookie['cookie_http']);
                    setcookie($cookname . '_REMEMBER', 'rememberme',
                        time() + $this->cookie['cookie_max_exp'],
                        $this->cookie['cookie_path'],
                        null, $this->cookie['cookie_secure'],
                        $this->cookie['cookie_http']);
                } else {
                    setcookie($cookname . '_COOKID', $uid,
                        time() + $this->cookie['cookie_exp'],
                        $this->cookie['cookie_path'], null,
                        $this->cookie['cookie_secure'],
                        $this->cookie['cookie_http']);
                    setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $this->cookie['cookie_exp'],
                        $this->cookie['cookie_path'], null,
                        $this->cookie['cookie_secure'],
                        $this->cookie['cookie_http']);
                }
                $result = array(
                    'code' => '0',
                    'msg' => 'OK'
                );
            } else {
                throw new \Exception("USR_PASS_ERR", 1);
            }
        } catch (\Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        echo json_encode($result);
    }

    public function islogged()
    {
        if ($this->type == 'admins') {
            $checktable = 'admins';
            $cookname = $this->cookie['admin'];
        } elseif ($this->type == 'users') {
            $checktable = 'users';
            $cookname = $this->cookie['user'];
        }
		$typeid = $checktable . '.id';
        $typeuser = $checktable . '.user';
		$typetype = $checktable . '.type';
		$typesuper = $checktable . '.superuser';
		$typestato = $checktable . '.stato';
        $cookietype = $cookname . '_COOKID';
        $cookiepub = $cookname . '_PUB';
        if (!isset($_COOKIE[$cookietype])) {
            return false;
        }
        $uid = $_COOKIE[$cookietype];
        $result = $this->db->select('cookies', [
            '[><]' . $this->type => [
                'uid' => 'id'
            ]
        ], [
			'cookies.uid',
            'cookies.auth_token',
			$typeid,
            $typeuser,
			$typetype,
			$typesuper
        ], [
            'cookies.uuid' => $uid,
            'cookies.type' => $this->type,
			$typestato => 0
        ]);
        $cookiesalt = $_COOKIE[$cookiepub];
        $concat_string = $_SERVER['HTTP_USER_AGENT'] . ':~:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ':~:' . $cookiesalt;
        $token = base64_encode($concat_string);
        if (password_verify($token, $result[0]['auth_token'])) {
			$logintype = $this->type .'_';
            $checksu = $this->decrypt($result[0]['superuser'], $this->secure['crypt_salt']);
			self::$id = $result[0]['id'];
			self::$user = $result[0]['user'];
			self::$priv = $result[0]['type'];
			self::$super = ($result[0]['user'] == $checksu ? true : false);
            return true;
        } else {
            $this->db->delete('cookies', [
                "AND" => [
                    "uuid" => $uid,
                    "type" => $this->type
                ]
            ]);
            session_unset();
            session_destroy();
            setcookie($cookname . '_COOKID', $uid,
                time() - $this->cookie['cookie_max_exp'],
                $this->cookie['cookie_path'], null,
                $this->cookie['cookie_secure'],
                $this->cookie['cookie_http']);
            setcookie($cookname . '_PUB', $cookiesalt,
                time() - $this->cookie['cookie_max_exp'],
                $this->cookie['cookie_path'], null,
                $this->cookie['cookie_secure'],
                $this->cookie['cookie_http']);
            setcookie($cookname . '_REMEMBER', 'rememberme',
                time() - $this->cookie['cookie_max_exp'],
                $this->cookie['cookie_path'],
                null, $this->cookie['cookie_secure'],
                $this->cookie['cookie_http']);
        }
        return false;
    }

    public function decrypt($string, $key)
    {
        $c = base64_decode($string);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
    }

    public function randStr($len)
    {
        $string1 = md5(rand());
        $string2 = $this->create_hash($string1);
        $string3 = explode("$", $string2);
        $string4 = implode("/", array_slice($string3, 3));
        $result = preg_replace("/[^A-Za-z0-9]/", '', $string4);
        return trim(substr($result, 0, $len));
    }

    public function create_hash($string)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($string, PASSWORD_BCRYPT, $options);
    }

    public function logout()
    {
        try {
			if ($this->type == "admins") {
				$cookname = $this->cookie['admin'];
            } else {
                $cookname = $this->cookie['user'];
            }
            $cookietype = $cookname . '_COOKID';
            $uid = $_COOKIE[$cookietype];
            if ($uid) {
                $this->db->delete('cookies', [
                    "AND" => [
                        "uuid" => $uid,
                        "type" => $this->type
                    ]
                ]);
                session_unset();
                session_destroy();
                setcookie($cookname . '_COOKID', '',
                    time() - $this->cookie['cookie_max_exp'],
                    $this->cookie['cookie_path'], null,
                    $this->cookie['cookie_secure'],
                    $this->cookie['cookie_http']);
                setcookie($cookname . '_PUB', '',
                    time() - $this->cookie['cookie_max_exp'],
                    $this->cookie['cookie_path'], null,
                    $this->cookie['cookie_secure'],
                    $this->cookie['cookie_http']);
                setcookie($cookname . '_REMEMBER', '',
                    time() - $this->cookie['cookie_max_exp'],
                    $this->cookie['cookie_path'], null,
                    $this->cookie['cookie_secure'],
                    $this->cookie['cookie_http']);
                $result = array(
                    'code' => '0',
                    'msg' => 'OK'
                );
            } else {
                throw new \Exception("NO_COOKIE", 1);
            }
        } catch (\Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return json_encode($result);
    }
}