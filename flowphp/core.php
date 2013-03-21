<?php

/**
 * flowphp核心文件
 * 初始化各项参数
 * @author princehaku
 * @site http://3haku.net
 */

$GLOBALS['_beginTime'] = microtime(true);

date_default_timezone_set('Asia/Chongqing');

@session_start();
// 定义路径
if (!defined("APP_PATH")) {
    throw new Exception("No APP_PATH Defined");
}