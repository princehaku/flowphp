<?php

/** 数据验证类
 * 
 * @author princehaku
 * @site http://3haku.net
 */

class Validate {

    private static $regex = array(
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', 
        'phone' => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', 
        'mobile' => '/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/', 
        'url' => '/^http[s]:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', 
        'currency' => '/^\d+(\.\d+)?$/', 'number' => '/\d+$/', 
        'qq' => '/^[1-9]\d{4,13}$/', 
        'double' => '/^[-\+]?\d+(\.\d+)?$/', 
        'english' => '/^[A-Za-z]+$/'
    );

    private static function getRegex($name) {
        if (isset(self::$regex[strtolower($name)])) {
            return self::$regex[strtolower($name)];
        } else {
            return $name;
        }
    }

    public static function check($value, $checkName) {
        $matchRegex = self::getRegex($checkName);
        return preg_match($matchRegex, trim($value));
    }

}
