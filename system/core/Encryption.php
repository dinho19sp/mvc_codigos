<?php
final class Encryption {
    private $key;
    private $iv;
    private $_caracter = "";
    private $_array_caracter;
    private $_size_length;
    private $_keys;

    public function __construct($key = NULL) {
    $this->key = hash('sha256', $key, true);
            $this->iv = mcrypt_create_iv(32, MCRYPT_RAND);
    }

    public function encrypt($value) {
            return strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $value, MCRYPT_MODE_ECB, $this->iv)), '+/=', '-_,');
    }

    public function decrypt($value) {
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, base64_decode(strtr($value, '-_,', '+/=')), MCRYPT_MODE_ECB, $this->iv));
    }


    /**
     * @param $size quantidade de caracteres
     * @param $knd alp - alfabético, num - numérico, alpnum - alfanumérico
     * @return string
     */
    public function code_generator($size, $knd)
    {
        $this->_size_length = $size;
        $this->_keys = $knd;


        $this->_array_caracter = array(
                'alp' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'num' => '0123456789',
                'alpnum' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        );


        for ($i = 0; $i < $this->_size_length; $i++)
        {

            $this->_caracter .= $this->_array_caracter[$this->_keys][rand(0, strlen($this->_array_caracter[$this->_keys]) - 1)];

        }

        return ($this->_caracter);
    }

    public function setSecurityCode()
    {
        $user_codigo = $this->code_generator(25,'alpnum');

        $security_code = substr(chunk_split($user_codigo,5,"-"),0,-1);

        return $security_code;
    }
}