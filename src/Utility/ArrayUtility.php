<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 16/04/2018
 * Time: 22:54
 */

namespace HeraldryEngine\Utility;


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
}