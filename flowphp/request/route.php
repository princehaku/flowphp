<?php

/**
 * url分发器
 * 支持rewrite,pathinfo和普通get方式
 *
 * @author princehaku
 * @site http://3haku.net
 */

class F_Request_Route {

    protected $action;

    protected $method;

    public function init() {

        $action = 'Main';
        $method = 'index';

        // 是否使用path info
        $url = $_SERVER['REQUEST_URI'];
        $url = basename($url);
        if (!empty($_GET['action'])) {
            $action = $_GET['action'];
            $url = '';
        }
        if (!empty($_GET['method'])) {
            $method = $_GET['method'];
            $url = '';
        }
        $url = str_replace(APP_PATH, "", $url);
        // 拆开?
        $url = explode("?", $url);

        $url = $url[0];
        // 按斜杠分拆
        $params = explode("/", $url);

        if (!empty($params[1])) {
            $action = $params[1];
        }
        if (!empty($params[2])) {
            $method = $params[2];
        }
        // 设置到请求里面
        foreach ($params as $i => $j) {
            if ($i % 2 == 0 && $i != 0) {
                $_GET[$params[$i - 1]] = $params[$i];
            }
        }

        $this->action = basename($action);
        $this->method = basename($method);
    }

    public function getAction() {
        return $this->action;
    }

    public function getMethod() {
        return $this->method;
    }

}