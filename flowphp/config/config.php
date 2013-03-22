<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * 系统基础配置信息
 *
 */
return array(
    // 缓存目录
    "CACHE_DIR" => APP_PATH . "/appcache/",
    // 模板目录
    "VIEW_DIR" => APP_PATH . "/template/",
    // 强制注销REQUEST
    "UNSET_REQS" => 1,
    // URL 分发器
    "URL_DISPACHER" => "sys",

    "TRACE_ERROR" => 1,

    "TPL_ENGINE" => "F_View_SViewEngine"
);