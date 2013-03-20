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
// 加载日志类
include_once ("core/log/Log.class.php");
// 定义路径
if (!defined("APP_PATH")) {
    define("APP_PATH", dirname($_SERVER['PHP_SELF']));
}
if (!defined("CORE_BASE")) {
    define("CORE_BASE", dirname(__FILE__));
}
// 定义站点根目录
define("APP_BASE", $_SERVER['DOCUMENT_ROOT'] . APP_PATH);

// 加载系统默认配置文件
include_once (CORE_BASE . "/config/config.php");

$config = array();
// 加载程序默认配置文件
// 加载所有配置文件
if (file_exists(APP_BASE . "/config/")) {
    $config_files = scandir(APP_BASE . "/config/");
    foreach ($config_files as $i => $config_file) {
        $r = explode(".", $config_file);
        $ext = $r[count($r) - 1];
        if ($ext == "php" && file_exists(APP_BASE . "/config/" . $config_file)) {
            include_once (APP_BASE . "/config/" . $config_file);
        }
    }
    // 合并配置文件
    $config = array_merge($sysconfig, $config);
}
// 加载数据库类
include_once (CORE_BASE . "/core/db/DB.class.php");

// 加载flowphp公用函数
$function_files = scandir(CORE_BASE . "/common/");

foreach ($function_files as $i => $function_file) {
    $r = explode(".", $function_file);
    $ext = $r[count($r) - 1];
    if ($ext == "php" && file_exists(CORE_BASE . "/common/" . $function_file)) {
        include_once (CORE_BASE . "/common/" . $function_file);
    }
}

// 加载所有程序公共文件
if (file_exists(APP_BASE . "/common/")) {
    $function_files = scandir(APP_BASE . "/common/");
    foreach ($function_files as $i => $function_file) {
        $r = explode(".", $function_file);
        $ext = $r[count($r) - 1];
        if ($ext == "php" && file_exists(APP_BASE . "/common/" . $function_file)) {
            L()->i("加载文件" . $function_file);
            include_once (APP_BASE . "/common/" . $function_file);
        }
    }
}

// 配置异常处理
if (C('DEBUG') != "" && C('DEBUG') == 0) {
    error_reporting(0);
    ini_set("display_errors", "0");
    C("LOG_PRI", 0);
} else {
    ini_set("display_errors", "1");

    error_reporting(E_ALL);

    import("core.exception.FlowErrors");

    set_error_handler("FlowErrors::errorHandler");

    register_shutdown_function("FlowErrors::fatalHandler");
}
// 关闭zend的php4兼容
if (ini_get('zend.ze1_compatibility_mode') == true) {
    ini_set('zend.ze1_compatibility_mode', 0);
}
// 如果没有siteurl 则设置为访问域名
if (C('SITE_URL') == "") {
    C('SITE_URL', "http://" . $_SERVER['HTTP_HOST']);
    L()->i("没有在config.php指定SITE_URL");
}
// 加载基类
import("core.flowphp");
// ---------初始化完毕-----------
