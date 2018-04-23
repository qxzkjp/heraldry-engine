<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 23/04/2018
 * Time: 20:08
 */

namespace HeraldryEngine\Utility;


class RandomString
{
    public static function generate($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ,;:.';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}