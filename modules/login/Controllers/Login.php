<?php

namespace login\Controllers;

class Login
{
    public static function encrypt($string, $key)
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext_base64 = base64_encode($iv . $hmac . $ciphertext_raw);
        return trim($ciphertext_base64);
    }

    public function check($container)
    {
        try {
            $typesArray = array(
                'admins',
                'users'
            );
            $type = (in_array($container->get('Sanitizer')->POST('type', 'alpha'),
                $typesArray) ? $container->get('Sanitizer')->POST('type', 'alpha') : 'admins');
            $db = $container->get('db');
            /*if ($this->islogged($type)) {
                throw new Exception("OK", 0);
            }*/
            $username = $container->get('Sanitizer')->POST('username', 'user');
            $password = $container->get('Sanitizer')->POST('password', 'pass');
            $remember = $container->get('Sanitizer')->POST('rememberme', 'int') == 1 ? false : true;
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
            if (password_verify($password, $result[0]['pass'])) {
                $id = $result[0]['id'];
                $user = $result[0]['user'];
                $privil = $result[0]['privilegi'];
                if ($type == "admins") {
                    $cookname = $container->get('settings.cookie')['admin'];
                } else {
                    $cookname = $container->get('settings.cookie')['user'];
                }
                $cookiesalt = self::randStr(100);
                $concat_string = $_SERVER['HTTP_USER_AGENT'] . ':~:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ':~:' . $cookiesalt;
                $token_first = base64_encode($concat_string);
                $tokenhash = self::create_hash($token_first);
                $uid = substr(preg_replace("/[^A-Za-z0-9]/", '', self::create_hash($id)), 11);
                $db->insert('cookies', [
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
                    \login\Controllers\Cookie::_setcookie($cookname . '_COOKID', $uid,
                        time() + $container->get('settings.cookie')['cookie_max_exp'],
                        $container->get('settings.cookie')['cookie_path'], null,
                        $container->get('settings.cookie')['cookie_secure'],
                        $container->get('settings.cookie')['cookie_http']);
                    \login\Controllers\Cookie::_setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $container->get('settings.cookie')['cookie_max_exp'],
                        $container->get('settings.cookie')['cookie_path'], null,
                        $container->get('settings.cookie')['cookie_secure'],
                        $container->get('settings.cookie')['cookie_http']);
                    \login\Controllers\Cookie::_setcookie($cookname . '_REMEMBER', 'rememberme',
                        time() + $container->get('settings.cookie')['cookie_max_exp'],
                        $container->get('settings.cookie')['cookie_path'],
                        null, $container->get('settings.cookie')['cookie_secure'],
                        $container->get('settings.cookie')['cookie_http']);
                } else {
                    \login\Controllers\Cookie::_setcookie($cookname . '_COOKID', $uid,
                        time() + $container->get('settings.cookie')['cookie_exp'],
                        $container->get('settings.cookie')['cookie_path'], null,
                        $container->get('settings.cookie')['cookie_secure'],
                        $container->get('settings.cookie')['cookie_http']);
                    \login\Controllers\Cookie::_setcookie($cookname . '_PUB', $cookiesalt,
                        time() + $container->get('settings.cookie')['cookie_exp'],
                        $container->get('settings.cookie')['cookie_path'], null,
                        $container->get('settings.cookie')['cookie_secure'],
                        $container->get('settings.cookie')['cookie_http']);
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
        echo json_encode($result);
    }

    public function islogged($container)
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
            $db->delete('cookies', [
                "AND" => [
                    "uuid" => $uid,
                    "type" => $type
                ]
            ]);
            session_unset();
            session_destroy();
            \login\Controllers\Cookie::_setcookie($cookname . '_COOKID', $uid,
                time() - $container->get('settings.cookie')['cookie_max_exp'],
                $container->get('settings.cookie')['cookie_path'], null,
                $container->get('settings.cookie')['cookie_secure'],
                $container->get('settings.cookie')['cookie_http']);
            \login\Controllers\Cookie::_setcookie($cookname . '_PUB', $cookiesalt,
                time() - $container->get('settings.cookie')['cookie_max_exp'],
                $container->get('settings.cookie')['cookie_path'], null,
                $container->get('settings.cookie')['cookie_secure'],
                $container->get('settings.cookie')['cookie_http']);
            \login\Controllers\Cookie::_setcookie($cookname . '_REMEMBER', 'rememberme',
                time() - $container->get('settings.cookie')['cookie_max_exp'],
                $container->get('settings.cookie')['cookie_path'],
                null, $container->get('settings.cookie')['cookie_secure'],
                $container->get('settings.cookie')['cookie_http']);
        }
        return false;
    }

    public static function randStr($len)
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
        $options = [
            'cost' => 12,
        ];
        return password_hash($string, PASSWORD_BCRYPT, $options);
    }
}