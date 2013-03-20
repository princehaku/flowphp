<?php

/** 包含js文件
 * 
 * @param string $name js文件名
 */
function jsinclude($namearray) {
    if (!is_array($namearray))
        return "<script type='text/javascript' src='" . C('SITE_URL') . C("JS_DIR") . "$namearray.js'></script>\n";
    else {
        $res = "";
        foreach ($namearray as $i => $j) {
            $res.=jsinclude($j);
        }
        return $res;
    }
}

/** 包含css文件
 * @param string $name css文件名
 */
function cssinclude($namearray) {
    if (!is_array($namearray))
        return '<link rel="stylesheet" type="text/css" href="' . C('SITE_URL') . C("CSS_DIR") . $namearray . '.css"/>' . "\n";
    else {
        $res = "";
        foreach ($namearray as $i => $j) {
            $res.=cssinclude($j);
        }
        return $res;
    }
}

/** 页面跳转 <meta> 方式的重定向
 * @param $url 跳转到的路径
 * @param time 间隔跳转的时间
 */
function gotourl($url, $time) {
    echo "<meta http-equiv='refresh' content=$time;url='$url'> ";
    //echo "please wait";
    return;
}

/** 根具时间戳得到一个标准化的datetime
 *
 * @param string $timeStamp
 */
function datetime($timeStamp) {
    $timeStamp = (int) $timeStamp;
    $datetime = date("Y-m-d H:i:s", $timeStamp);
    return $datetime;
}

/** 得到系统内目录
 * 如果存在返回本身
 * 如果不存在返回相对$basedir的路径
 * 注意:此路径可能不存在
 * @param string $dirname
 */
function getDirPath($dirname, $basedir = "") {
    if ($basedir == "") {
        $basedir = APP_BASE;
    }
    file_exists($dirname) ? $dirpath = $dirname : $dirpath = $basedir . $dirname;
    return $dirpath;
}

/** 转义html字符 htmlspecialchars 包含括号
 * @param string|array $s
 */
function htmlspecialchars_deep($s) {
    if (!is_array($s)) {
        $s = htmlspecialchars($s, ENT_QUOTES);
        return $s;
    } else {
        foreach ($s as $i => $j) {
            $s[$i] = htmlspecialchars_deep($j);
        }
        return $s;
    }
}

/** 加上引号
 *  对数组也有用
 * @param string|array $value
 */
function addslashes_deep($value) {
    $value = is_array($value) ?
            array_map('addslashes_deep', $value) :
            addslashes($value);
    return $value;
}

/** 反过滤被加上的引号
 *  对数组也有用
 * @param string|array $value
 */
function stripslashes_deep($value) {
    $value = is_array($value) ?
            array_map('stripslashes_deep', $value) :
            stripslashes($value);

    return $value;
}

/** 打印自定义errorpage
 *
 * @param type $code 
 */
function error_page($code) {
    if (!headers_sent()) {
        header("HTTP/1.1 $code NOT FOUND");
    }
    if (C("PAGE_$code")) {
        $v = new View();
        $v->display(C("PAGE_$code"));
    }
    exit();
}

/** 把正则符进行转义
 * 比如.转义为\.
 *
 * @param type $source 
 */
function regxp_convert($source) {
    $source = str_replace("\\", "\\\\", $source);
    $source = str_replace("/", "\\/", $source);
    $source = str_replace("$", "\\$", $source);
    $source = str_replace("[", "\[", $source);
    $source = str_replace("]", "\]", $source);
    $source = str_replace("(", "\(", $source);
    $source = str_replace(")", "\)", $source);
    $source = str_replace(".", "\.", $source);
    return $source;
}

/** 分拆url的参数为数组
 *
 * @param type $url 
 */
function http_parse_query($url, $decode = false) {
    $parsed_link = parse_url($url);
    if (empty($parsed_link["query"])) {
        return array();
    }
    return parse_query($parsed_link["query"], $decode);
}

/** 分拆参数为数组
 *
 * @param type $url 
 */
function parse_query($query, $decode = false) {

    $query_string = str_replace("?", "", $query);
    $querys = explode("&", $query_string);
    $params = array();

    foreach ($querys as $query) {
        $r = explode("=", $query);
        if (count($r) != 2) {
            continue;
        }
        $val = $r[1];
        if ($decode) {
            $val = urldecode($val);
        }
        $params[$r[0]] = $val;
    }

    return $params;
}
/**把一个对象转换成数组
 *
 * @param type $array
 * @return type 
 */
function obj2arr($array) {
    if (is_object($array)) {
        $array = (array) $array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = obj2arr($value);
        }
    }
    return $array;
}