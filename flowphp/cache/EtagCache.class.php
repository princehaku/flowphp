<?php

/**
 * 浏览器缓存控制器
 *
 * @author princehaku
 *
 */

class EtagCache {

    //缓存已经变更
    function changed() {
        if (!headers_sent()) {
            header("Cache-Control: max-age=54000");
            header("Etag:" . md5(time()));
        }
    }

    //缓存未变更
    function nochange() {
        if (!headers_sent()) {
            header("Cache-Control: max-age=54000");
            header("Etag:" . md5(time()));
            header('Last-Modified: ' . date('D, d M Y H:i:s', time()) . ' GMT', true, 304);
            die();
        }
    }

    function nocache() {
        if (!headers_sent()) {
            header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
            //header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
        }
    }

}
