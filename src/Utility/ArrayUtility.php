<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 16/04/2018
 * Time: 22:54
 */

namespace HeraldryEngine\Utility;


use Symfony\Component\HttpFoundation\Session\Session;

class ArrayUtility
{
    /**
     * @param mixed $key
     * @param array|\ArrayAccess $arr
     * @return bool
     */
    public static function OffsetExists($key, $arr){
        return is_array($arr) ? array_key_exists($key, $arr) : $arr->offsetExists($key);
    }

    /**
     * @param mixed $key
     * @param array|\ArrayAccess|Session $arr
     * @param mixed|null $default
     * @return mixed
     */
    public static function Get($key, $arr, $default = null){
        if($arr instanceof Session){
            return $arr->get($key, $default);
        }else if(is_array($arr)){
            return array_key_exists($key,$arr) ? $arr[$key] : $default;
        }else{
            return $arr->offsetExists($key) ? $arr[$key] : $default;
        }
    }
}