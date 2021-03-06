<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : Array.php
 *  Created on : 13-7-9 , 下午11:31
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Helper_Array {

    /**
     * 用arr2覆盖arr1相关数组
     */
    public static function MergeArray($arr1, $arr2) {

        if (empty($arr2) || !is_array($arr2)) {
            return $arr1;
        }

        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value))
                $arr1[$key] = self::MergeArray($arr1[$key], $arr2[$key]);
            else
                $arr1[$key] = $value;
        }

        return $arr1;
    }

    /**
     * 转义html字符 htmlspecialchars 包含括号
     *
     * @param string|array $s
     */
    public static function htmlspecialchars($s) {
        if (!is_array($s)) {
            $s = htmlspecialchars($s, ENT_QUOTES, 'ISO-8859-1');
            return $s;
        } else {
            foreach ($s as $i => $j) {
                $s[$i] = self::htmlspecialchars($j);
            }
            return $s;
        }
    }
}