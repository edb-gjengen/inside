<?php
/* Encryption library for 
 * Provided by Rasmus in SnapOrder */

class CryptoHelper
{

    private $iv;
    private $key;

    function __construct($iv, $key)
    {
        $this->iv = $iv;
        $this->key = $key;
        
    }

    public function encrypt($str)
    {
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return bin2hex($encrypted);
    }

    public function decrypt($code)
    {
        //return $code;
        $code = $this->hex2bin($code);
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $decrypted = mdecrypt_generic($td, $code);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return utf8_encode(trim($decrypted));
    }

    private function hex2bin($hexdata)
    {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

    public function json_encode_and_encrypt($data) {
        //return json_encode($data);
        return $this->encrypt(json_encode($data));
    }

}

