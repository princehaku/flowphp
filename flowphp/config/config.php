<?php

/** 系统配置信息
 *
 * @author princehaku
 * @site http://3haku.net
 */
$sysconfig = array(
    // 缓存目录
    "CACHE_DIR" =>  APP_BASE . "/appcache/",
    // 模板目录
    "VIEW_DIR" => APP_BASE . "/templates/",
    // 强制注销REQUEST
    "FORCE_REQUEST" => 1,
    // 强制使用linux的换行符
    "FORCE_UNINX_BR" => 1,
    // URL 分发器
    "URL_DISPACHER" => "sys",
);