<?php

/**
 * 系统函数库
 *
 * @author princehaku
 * @site http://3haku.net
 */

/**
 * 设置和获得配置的选项
 * 如果$value不为空 则是设置选项 并返回value
 * 注意 设置的配置非持久
 * @param $param
 * @param $value default null
 * @param &$config default null
 */
function C($param, $value = null, &$config = null) {

    global $config;

    if ($value !== null) {
        $config[$param] = $value;
        return $value;
    }

    if (is_array($param)) {
        $config = $param;
    }

    if (isset($config[$param])) {
        return $config[$param];
    }

    return null;
}

/**
 * 包含数据模型
 * @param $modleName 模型的名字 结尾需要是.class.php
 */
function M($modelName) {
    global $m;

    isset($m) ? $m : $m = array();

    if (!array_key_exists($modelName, $m)) {
        $t = include_once(APP_BASE . "/model/" . $modelName . ".class.php");

        if (!$t) {
            throw new FlowException("模型" . $modelName . "载入失败, 没有文件 " . APP_BASE . "/module/" . $modelName . ".class.php");
        } else {
            Flow::Log()->i("模型" . $modelName . "载入成功");
        }
        $m[$modelName] = new $modelName($modelName);
    }
    //$m->setFormName($modleName);

    return $m[$modelName];
}

/**
 * 得到一个数据库连接
 * 唯一 每次都是同一个数据库连接
 * @return $db
 */
function D($config = null) {
    global $db;
    if ($db == null) {
        $db = new DB($config);
    }
    return $db;
}

/**
 * 多语言支持
 * @param $phrase 词根
 * @param $phrase 语言标识符
 */
function _e($phrase, $loc = "cn") {

    $langtmp = array();

    global $langtmp;
    //伪单例模式  只载入一次语言文件
    if (empty($langtmp[$loc])) {
        $langtmp[$loc] = array();

        if (file_exists(CORE_BASE . "/lang/lang_$loc.php")) {
            include_once(CORE_BASE . "/lang/lang_$loc.php");
            $langtmp[$loc] = array_merge($langtmp[$loc], $lang);
            $lang = array();
        }

        if (file_exists(APP_BASE . "/lang/lang_$loc.php")) {
            include_once(APP_BASE . "/lang/lang_$loc.php");
            $langtmp[$loc] = array_merge($langtmp[$loc], $lang);
            $lang = array();
        }
    }
    $phrase = (string) $phrase;

    if (isset($langtmp[$loc][$phrase])) {
        return $langtmp[$loc][$phrase];
    } else {
        return "No Such Phrase";
    }

    //打开语言文件
}

/**
 * 包含一个基类库
 * 优先包含系统路径
 * @param string $path 定义为 xx.xxx.xx  结尾需要是.class.php
 */
function import($path) {
    $r = explode(".", $path);
    $url = "";
    foreach ($r as $i => $j) {
        $url = $url . "/" . $j;
    }
    $loadfile = CORE_BASE . $url . ".class.php";

    if (file_exists($loadfile)) {
    } else {
        $loadfile = APP_BASE . $url . ".class.php";

        if (file_exists($loadfile)) {
        } else {
            throw new Exception("库文件 $path 不存在");
            return false;
        }
    }
    include_once $loadfile;
    return true;
}