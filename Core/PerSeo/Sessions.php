<?php

namespace PerSeo;
use SessionHandler;

class Sessions extends SessionHandler
{
	protected static $cipher = "AES-256-CBC";
	
    private $key;
	
	protected static function encrypt($data, $password) {
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
		$iv  = substr($salted, 32,16);

		$encrypted_data = openssl_encrypt($data, self::$cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		return base64_encode($salt . $encrypted_data);
	}

	protected static function decrypt($edata, $password) {
		$data = base64_decode($edata);
		$salt = substr($data, 0, 16);
		$ct = substr($data, 16);

		$rounds = 3; // depends on key length
		$data00 = $password.$salt;
		$hash = array();
		$hash[0] = hash('sha256', $data00, true);
		$result = $hash[0];
		for ($i = 1; $i < $rounds; $i++) {
			$hash[$i] = hash('sha256', $hash[$i - 1].$data00, true);
			$result .= $hash[$i];
		}
		$key = substr($result, 0, 32);
		$iv  = substr($result, 32,16);

		return openssl_decrypt($ct, self::$cipher, $key, true, $iv);
	}	

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function read($id)
    {
        $data = parent::read($id);

        if (!$data) {
            return "";
        } else {
            return self::decrypt($data, $this->key);
        }
    }

    public function write($id, $data)
    {
        $data = self::encrypt($data, $this->key);

        return parent::write($id, $data);
    }
	
}