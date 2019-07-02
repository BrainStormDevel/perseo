<?php

namespace login\Controllers;

class Login
{
    protected static $id = '';
    protected static $name = '';
    protected static $superuser = '';
    protected static $privileges = '';
	protected $container;
    protected $type;
	protected $db;
	
	public function __construct($container, $type)
    {
        $this->container = $container;
        $this->type = $type;
		$this->db = $container->get('db');
    }

    public function id()
    {
        return self::$id;
    }

    public function username()
    {
        return self::$name;
    }

    public function superuser()
    {
        return self::$superuser;
    }

    public function privileges()
    {
        return self::$privileges;
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

    public function check()
    {
        try {
			if ($this->islogged($this->type)) {
                throw new \Exception("OK", 0);
            }
            $username = $this->container->get('Sanitizer')->POST('username', 'user');
            $password = $this->container->get('Sanitizer')->POST('password', 'pass');
            $remember = $this->container->get('Sanitizer')->POST('rememberme', 'int') == 1 ? false : true;
            if (!$username or !$password) {
                throw new \Exception("USR_PASS_EMPTY", 1);
            }
			$result = $this->db->select($this->type, [
                'id',
                'user',
                'pass',
                'privilegi'
            ], [
                'user' => $username
            ]);
            $error = $this->db->error();
            if (($error[1] != null) && ($error[2] != null)) {
                throw new \Exception($error[2], 1);
            }
			if (password_verify($password, $result[0]['pass'])) {
                $id = $result[0]['id'];
                $user = $result[0]['user'];
                $privil = $result[0]['privilegi'];
                if ($this->type == "admins") {
                    $cookname = $this->container->get('settings.cookie')['admin'];
                } else {
                    $cookname = $this->container->get('settings.cookie')['user'];
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
                        time() + $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    setcookie($cookname . '_REMEMBER', 'rememberme',
                        time() + $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'],
                        null, $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                } else {
                    setcookie($cookname . '_COOKID', $uid,
                        time() + $this->container->get('settings.cookie')['cookie_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $this->container->get('settings.cookie')['cookie_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
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

    public function islogged($type)
    {
        if ($type == 'admins') {
            $checktable = 'admins';
            $cookname = ($this->container->has('settings.cookie') ? $this->container->get('settings.cookie')['admin'] : '');
        } elseif ($type == 'users') {
            $checktable = 'users';
            $cookname = ($this->container->has('settings.cookie') ? $this->container->get('settings.cookie')['user'] : '');
        }
        $cookietable = $checktable . '.user';
        $cookietype = $cookname . '_COOKID';
        $cookiepub = $cookname . '_PUB';
        if (!isset($_COOKIE[$cookietype])) {
            unset($_SESSION['logged_in']);
            return false;
        }
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
            return true;
        }
        $uid = $_COOKIE[$cookietype];
        $result = $this->db->select('cookies', [
            '[><]' . $type => [
                'uid' => 'id'
            ]
        ], [
            'cookies.auth_token',
            $cookietable
        ], [
            'uuid' => $uid,
            'type' => $type
        ]);
        $cookiesalt = $_COOKIE[$cookiepub];
        $concat_string = $_SERVER['HTTP_USER_AGENT'] . ':~:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ':~:' . $cookiesalt;
        $token = base64_encode($concat_string);
        if (password_verify($token, $result[0]['auth_token'])) {
            $_SESSION['logged_in'] = 1;
            $result2 = $this->db->select($type, [
                'id',
                'superuser',
                'privilegi'
            ], [
                'id' => $result[0]['id'],
                'stato' => 0
            ]);
            $checksu = $this->decrypt($result2[0]['superuser'],
                ($this->container->has('settings.secure') ? $this->container->get('settings.secure')['crypt_salt'] : ''));
            self::$id = $result2[0]['id'];
            self::$name = $result[0]['user'];
            self::$superuser = ($result[0]['user'] == $checksu ? true : false);
            self::$privileges = $result2[0]['privilegi'];
            return true;
        } else {
            $this->db->delete('cookies', [
                "AND" => [
                    "uuid" => $uid,
                    "type" => $type
                ]
            ]);
            session_unset();
            session_destroy();
            setcookie($cookname . '_COOKID', $uid,
                time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                $this->container->get('settings.cookie')['cookie_path'], null,
                $this->container->get('settings.cookie')['cookie_secure'],
                $this->container->get('settings.cookie')['cookie_http']);
            setcookie($cookname . '_PUB', $cookiesalt,
                time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                $this->container->get('settings.cookie')['cookie_path'], null,
                $this->container->get('settings.cookie')['cookie_secure'],
                $this->container->get('settings.cookie')['cookie_http']);
            setcookie($cookname . '_REMEMBER', 'rememberme',
                time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                $this->container->get('settings.cookie')['cookie_path'],
                null, $this->container->get('settings.cookie')['cookie_secure'],
                $this->container->get('settings.cookie')['cookie_http']);
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

    public function logout($type)
    {
        try {
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
                if ($type == "admins") {
                    $cookname = $this->container->get('settings.cookie')['admin'];
                } else {
                    $cookname = $this->container->get('settings.cookie')['user'];
                }
                $cookietype = $cookname . '_COOKID';
                $uid = $_COOKIE[$cookietype];
                if ($uid) {
                    $this->db->delete('cookies', [
                        "AND" => [
                            "uuid" => $uid,
                            "type" => $type
                        ]
                    ]);
                    session_unset();
                    session_destroy();
                    setcookie($cookname . '_COOKID', '',
                        time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    setcookie($cookname . '_PUB', '',
                        time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    setcookie($cookname . '_REMEMBER', '',
                        time() - $this->container->get('settings.cookie')['cookie_max_exp'],
                        $this->container->get('settings.cookie')['cookie_path'], null,
                        $this->container->get('settings.cookie')['cookie_secure'],
                        $this->container->get('settings.cookie')['cookie_http']);
                    $result = array(
                        'code' => '0',
                        'msg' => 'OK'
                    );
                } else {
                    throw new Exception("NO_COOKIE", 1);
                }
            }
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return json_encode($result);
    }
}