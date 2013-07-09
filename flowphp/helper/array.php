<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : array.php
 *  Created on : 13-7-9 , 下午11:31
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Helper_Array {

    public static function MergeArray($arr1, $arr2) {

        if (empty($arr2) || !is_array($arr2)) {
            return $arr1;
        }

        foreach ($arr1 as $key => $value) {
            if (array_key_exists($key, $arr2) && is_array($value)) {
                $arr1[$key] = self::MergeArray($arr1[$key], $arr2[$key]);
                unset($arr2[$key]);
            }
            else {
                $arr1[$key] = $value;
            }
        }
        $arr1 = $arr1 + $arr2;

        return $arr1;
    }
}