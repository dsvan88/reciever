<?php
class Crypt{
    public $key;
    public  $encoded;
    public  $decoded;
    public function __construct($options = ['value' => 'password']){
        if (!isset($options['key'])){
            $this->key = $this->prepKey($options['value']);
            $this->encrypt($options['value']);
        }else{
            $this->key = $options['key'];
            if (isset($options['value']))
                $this->decoded = $this->decrypt($options['value']);
        }
    }
    private function prepKey($value){
        return str_shuffle(sha1(bin2hex(random_bytes(strlen($value)*random_int(5,10)))));
    }
    public function encrypt($value){
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encoded_raw = openssl_encrypt($value, $cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $this->encoded = base64_encode( $iv.$encoded_raw );
        return $this->encoded;
    }
    public function decrypt($value){
        $encodedBase64 = base64_decode($value);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($encodedBase64, 0, $ivlen);
        $encoded_raw = substr($encodedBase64, $ivlen);
        $this->decoded = openssl_decrypt($encoded_raw, $cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        return $this->decoded;
    }
}