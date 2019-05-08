<?php

namespace PerSeo;

use Exception;

class Login implements LoginInterface
{

    protected static $CRYPT_SALT = CRYPT_SALT;
    protected static $id = '';
    protected static $name = '';
    protected static $superuser = '';
    protected static $privileges = '';
    private static $loggedadm = false;
    private static $loggedusr = false;
    private $_table = 'cookies';

    public static function encrypt($string, $key)
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext_base64 = base64_encode($iv . $hmac . $ciphertext_raw);
        return trim($ciphertext_base64);
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

    public function login($username, $password, $type, $remember = null)
    {
        try {
            $db = new DB();
            if ($this->islogged($type)) {
                throw new Exception("OK", 0);
            }
            if (!$username or !$password) {
                throw new Exception("USR_PASS_EMPTY", 1);
            }
            $result = $db->select($type, [
                'id',
                'user',
                'pass',
                'privilegi'
            ], [
                'user' => $username
            ]);
            $error = $db->error();
            if (($error[1] != null) && ($error[2] != null)) {
                throw new Exception($error[2], 1);
            }
            if (self::validate_hash($password, $result[0]['pass'])) {
                $id = $result[0]['id'];
                $user = $result[0]['user'];
                $privil = $result[0]['privilegi'];
                if ($type == "admins") {
                    $cookname = ADM_COOKNAME;
                } else {
                    $cookname = USR_COOKNAME;
                }
                $cookiesalt = $this->randStr(100);
                $concat_string = $_SERVER['HTTP_USER_AGENT'] . ':~:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ':~:' . $cookiesalt;
                $token_first = base64_encode($concat_string);
                $tokenhash = self::create_hash($token_first);
                $uid = substr(preg_replace("/[^A-Za-z0-9]/", '', self::create_hash($id)), 11);
                $db->insert($this->_table, [
                    "uid" => $id,
                    "uuid" => $uid,
                    "type" => $type,
                    "auth_token" => $tokenhash
                ]);
                $error = $db->error();
                if (($error[1] != null) && ($error[2] != null)) {
                    throw new Exception($error[2], 1);
                }
                if ($remember) {
                    $this->_setcookie($cookname . '_COOKID', $uid, time() + COOKIE_MAX_EXPIRE, COOKIE_PATH, null,
                        COOKIE_SECURE, COOKIE_HTTP);
                    $this->_setcookie($cookname . '_PUB', $cookiesalt, time() + COOKIE_MAX_EXPIRE, COOKIE_PATH, null,
                        COOKIE_SECURE, COOKIE_HTTP);
                    $this->_setcookie($cookname . '_REMEMBER', 'rememberme', time() + COOKIE_MAX_EXPIRE, COOKIE_PATH,
                        null, COOKIE_SECURE, COOKIE_HTTP);
                } else {
                    $this->_setcookie($cookname . '_COOKID', $uid, time() + COOKIE_EXPIRE, COOKIE_PATH, null,
                        COOKIE_SECURE, COOKIE_HTTP);
                    $this->_setcookie($cookname . '_PUB', $cookiesalt, time() + COOKIE_EXPIRE, COOKIE_PATH, null,
                        COOKIE_SECURE, COOKIE_HTTP);
                }
                $result = array(
                    'code' => '0',
                    'msg' => 'OK'
                );
            } else {
                throw new Exception("USR_PASS_ERR", 1);
            }
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return json_encode($result);
    }

    public function islogged($type)
    {
        $db = new DB();
        if ($type == 'admins') {
            $checktable = 'admins';
            $cookname = constant("ADM_COOKNAME");
        } elseif ($type == 'users') {
            $checktable = 'users';
            $cookname = constant("USR_COOKNAME");
        }
        $cookietable = $checktable . '.user';
        $cookietype = $cookname . '_COOKID';
        $cookiepub = $cookname . '_PUB';
        $uid = $_COOKIE[$cookietype];
        if (!$uid) {
            return false;
        }
        $result = $db->select($this->_table, [
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
        if (self::validate_hash($token, $result[0]['auth_token'])) {
            $result2 = $db->select($type, [
                'id',
                'superuser',
                'privilegi'
            ], [
                'id' => $result[0]['id'],
                'stato' => 0
            ]);
            $checksu = self::decrypt($result2[0]['superuser'], CRYPT_SALT);
            self::$id = $result2[0]['id'];
            self::$name = $result[0]['user'];
            self::$superuser = ($result[0]['user'] == $checksu ? true : false);
            self::$privileges = $result2[0]['privilegi'];
            return true;
        } else {
            $db->delete($db->_table, [
                "AND" => [
                    "uuid" => $uid,
                    "type" => $type
                ]
            ]);
            session_unset();
            session_destroy();
            $this->_setcookie($cookname . '_COOKID', '', time() - COOKIE_EXPIRE, COOKIE_PATH, null, COOKIE_SECURE,
                COOKIE_HTTP);
            $this->_setcookie($cookname . '_PUB', '', time() - COOKIE_EXPIRE, COOKIE_PATH, null, COOKIE_SECURE,
                COOKIE_HTTP);
            $this->_setcookie($cookname . '_REMEMBER', '', time() - COOKIE_EXPIRE, COOKIE_PATH, null, COOKIE_SECURE,
                COOKIE_HTTP);
        }
        return false;
    }

    public static function validate_hash($string, $correct_hash)
    {
        return hash_equals($correct_hash, crypt($string, $correct_hash));
    }

    public static function decrypt($string, $key)
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

    public function _setcookie($name, $value, $expire, $path, $domain, $secure = false, $httponly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public function randStr($len)
    {
        $string1 = md5(rand());
        $string2 = self::create_hash($string1);
        $string3 = explode("$", $string2);
        $string4 = implode("/", array_slice($string3, 3));
        $result = preg_replace("/[^A-Za-z0-9]/", '', $string4);
        return trim(substr($result, 0, $len));
    }

    public static function create_hash($string)
    {
        $salt = (PHP_MAJOR_VERSION >= 7 ? base64_encode(random_bytes(24)) : base64_encode(mcrypt_create_iv('24',
            MCRYPT_DEV_URANDOM)));
        return crypt($string, '$6$rounds=5000$' . $salt . '$');
    }

    public function logout($type)
    {
        try {
            $db = new DB();
            if ($type == "admins") {
                $cookname = ADM_COOKNAME;
            } else {
                $cookname = USR_COOKNAME;
            }
            $cookietype = $cookname . '_COOKID';
            $uid = $_COOKIE[$cookietype];
            if ($uid) {
                $db->delete($this->_table, [
                    "AND" => [
                        "uuid" => $uid,
                        "type" => $type
                    ]
                ]);
                session_unset();
                session_destroy();
                $this->_setcookie($cookname . '_COOKID', '', time() - COOKIE_MAX_EXPIRE, COOKIE_PATH, null,
                    COOKIE_SECURE, COOKIE_HTTP);
                $this->_setcookie($cookname . '_PUB', '', time() - COOKIE_MAX_EXPIRE, COOKIE_PATH, null, COOKIE_SECURE,
                    COOKIE_HTTP);
                $this->_setcookie($cookname . '_REMEMBER', '', time() - COOKIE_MAX_EXPIRE, COOKIE_PATH, null,
                    COOKIE_SECURE, COOKIE_HTTP);
                $result = array(
                    'code' => '0',
                    'msg' => 'OK'
                );
            } else {
                throw new Exception("NO_COOKIE", 1);
            }
        } catch (Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return json_encode($result);
    }

    public function __destruct()
    {
        self::$id = null;
        self::$name = null;
        self::$superuser = null;
        self::$privileges = null;
    }
}