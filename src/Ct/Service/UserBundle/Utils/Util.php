<?php

namespace Ct\Sercice\UserBundle\Utils;

class Util
{
    /**
     * Génération mot de passe
     * @param integer $l
     * @return string
     */
    public static function generatePassword($l)
    {
        $caracter        = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
        $specialCaracter = str_split('!/\@#$^&*()?');

        shuffle($caracter); 
        shuffle($specialCaracter);

        $rand           = '';
        $mergedCaracter = array();
        foreach (array_rand($caracter, ($l-1)) as $k) $mergedCaracter[] = $caracter[$k];
        $mergedCaracter[] = $specialCaracter[array_rand($specialCaracter, 1)];
        shuffle($mergedCaracter); 
        foreach (array_rand($mergedCaracter, $l) as $i) $rand .= $mergedCaracter[$i];
    
        return $rand;
    }
    
    /**
     * Génération token
     * @return type
     */
    public static function generateToken()
    {
        // http://stackoverflow.com/questions/18830839/generating-cryptographically-secure-tokens
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        return $token;
    }
}

