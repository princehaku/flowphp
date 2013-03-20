<?php

/** 系统配置信息
 *
 * @author princehaku
 * @site http://3haku.net
 */
$sysconfig = array(
    // 是否开启debug模式  注意 对active类型的action无效
    "DEBUG" => 1,
    // 数据库是否使用持久连接
    "DB_PERSISTANT" => 0,
    // 是否启用pathinfo优先解析URL
    "PATH_INFO" => "0",
    // 记录LOG的等级 1 ERROR 2 WARN 3 INFO 0 NONE
    "LOG_PRI" => 3,
    // 是否打印日志 注意 对active类型的action无效
    "SHOW_LOG" => 1,
    // 缓存目录
    "CACHE_DIR" =>  APP_BASE . "/cache/",
    // 模板目录
    "VIEW_DIR" => APP_BASE . "/templates/",
    // JS文件目录
    "JS_DIR" => APP_PATH . "/js/",
    // CSS文件目录
    "CSS_DIR" => APP_PATH . "/style/",
    // 强制注销REQUEST
    "FORCE_REQUEST" => 1,
    // 强制使用linux的换行符
    "FORCE_UNINX_BR" => 1,
    // URL 分发器
    "URL_DISPACHER" => "sys",
);