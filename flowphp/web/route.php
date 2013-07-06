<?php

/**
 * url分发器
 * 分发到对应的action和method
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Web_Route {

    protected $action;

    protected $method;

    public function getAction() {
        return $this->action;
    }

    public function getMethod() {
        return $this->method;
    }

    private function _routeRequest() {
        $action = 'Main';
        $method = 'index';

        $url = str_replace(dirname($_SERVER["SCRIPT_NAME"]), "", $_SERVER['REQUEST_URI']);
        $url_parsed = parse_url($url);

        if (!empty($_GET['action'])) {
            $action = $_GET['action'];
        }
        if (!empty($_GET['method'])) {
            $method = $_GET['method'];
        }

        // 按斜杠分拆
        $params = explode("/", trim($url_parsed['path'], "/"));

        if (!empty($params[0])) {
            $action = explode(".", $params[0]);
            $action = $action[0];
        }
        if (!empty($params[1])) {
            $method = explode(".", $params[1]);
            $method = $method[0];
        }
        // 设置到请求里面
        foreach ($params as $i => $j) {
            if ($i % 2 == 1 && $i != 0) {
                if ($params[$i - 1] != "") {
                    $_GET[$params[$i - 1]] = $params[$i];
                }
            }
        }

        $this->action = $action;

        $this->method = $method;
    }

    public function init() {
        $this->_routeRequest();
    }

}