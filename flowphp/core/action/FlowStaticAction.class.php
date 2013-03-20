<?php

/* * 静态文件控制器类
 * 一般用于js,css等不需客户浏览器每次更新的控制器
 * 特性:
 * 不会生成错误日志
 * 会使用Etag生成过期时间,防止客户端再次获取,加快载入速度
 * @author princehaku
 * @site http://3haku.net
 */

//载入模版引擎
import("core.view.View");
import("core.cache.EtagCache");

class FlowStaticAction extends FlowAction {

    public function display($viewname) {

        $cache = new EtagCache();

        // 模板文件
        $viewfilepath = APP_BASE . "/view/" . $viewname;

        // 缓存文件s
        $cachefilepath = APP_BASE . C("CACHE_DIR") . str_replace("/", "__", $viewname) . "__cache.php";

        if ($_SERVER['HTTP_IF_NONE_MATCH'] == "") {
            $cache->changed();
        }
        // 仅当客户端得到过文件 且缓存未变更
        if ($_SERVER['HTTP_IF_NONE_MATCH'] != "" && file_exists($cachefilepath) && file_exists($viewfilepath) && (filemtime($cachefilepath) > filemtime($viewfilepath))) {
            $cache->nochange();
        }
        // 显示模板
        $this->__view->display($viewname);
    }

}
