<?php

namespace PerSeo;

class Sessions extends \SessionHandler implements \SessionHandlerInterface, \SessionIdInterface
{
    protected static $cipher = 'AES-256-CBC';

    private $key;
    private $cookie;

    public function __construct($container)
    {
        $this->key = $container->get('settings.secure')['crypt_salt'];
        session_name('PERSEO_SESSID');
        session_set_cookie_params(
            0,
            $container->get('settings.cookie')['cookie_path'],
            null,
            $container->get('settings.cookie')['cookie_secure'],
            $container->get('settings.cookie')['cookie_http']
        );
    }

    public function read($id)
    {
        $data = parent::read($id);

        if (!$data) {
            return '';
        } else {
            return self::decrypt($data, $this->key);
        }
    }

    protected static function decrypt($edata, $password)
    {
        $data = base64_decode($edata);
        $salt = substr($data, 0, 16);
        $ct = substr($data, 16);

        $rounds = 3; // depends on key length
        $data00 = $password.$salt;
        $hash = [];
        $hash[0] = hash('sha256', $data00, true);
        $result = $hash[0];
        for ($i = 1; $i < $rounds; $i++) {
            $hash[$i] = hash('sha256', $hash[$i - 1].$data00, true);
            $result .= $hash[$i];
        }
        $key = substr($result, 0, 32);
        $iv = substr($result, 32, 16);

        return openssl_decrypt($ct, self::$cipher, $key, true, $iv);
    }

    public function create_sid()
    {
        return $this->get_rand();
    }

    private function get_rand()
    {
        $string = md5(rand());
        $options = [
            'cost' => 12,
        ];

        return preg_replace('/[^A-Za-z0-9]/', '', password_hash($string, PASSWORD_BCRYPT, $options));
    }

    public function write($id, $data)
    {
        $data = self::encrypt($data, $this->key);

        return parent::write($id, $data);
    }

    protected static function encrypt($data, $password)
    {
        // Set a random salt
        $ivlen = openssl_cipher_iv_length(self::$cipher);
        $salt = openssl_random_pseudo_bytes($ivlen);

        $salted = '';
        $dx = '';

        // Salt the key(32) and iv(16) = 48
        while (strlen($salted) < 48) {
            $dx = hash('sha256', $dx.$password.$salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);

        $encrypted_data = openssl_encrypt($data, self::$cipher, $key, $options = OPENSSL_RAW_DATA, $iv);

        return base64_encode($salt.$encrypted_data);
    }
}
