<?php

/**
 * url分发器
 * 分发到对应的action和method
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Web_Router {
    /**
     * 支持用正则的方式指定到一个新url上
     *
     * @var array
     */
    public $rewrite_rules = array();

    protected $action;

    protected $method;

    public function init() {
        $this->_routeRequest();
    }


    public function getAction() {
        return $this->action;
    }

    public function getMethod() {
        return $this->method;
    }

    private function _routeRequest() {
        $action = isset(Flow::$cfg['default_controller']) ? Flow::$cfg['default_controller'] : "index";
        $method = 'index';
        $base_path = Flow::app()->basePath;

        $uri = $_SERVER['REQUEST_URI'];

        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
        }
        // 如果有pattern 用pattern的匹配
        if (!empty($this->rewrite_rules)) {
            foreach ($this->rewrite_rules as $url_pattern => $dest) {
                $uri = preg_replace($url_pattern, $dest, $uri);
                break;
            }
        }
        $uri_parsed = parse_url($uri);
        if (empty($uri_parsed['path'])) {
            $uri_parsed['path'] = '/';
        }
        // 按斜杠分拆
        $params = explode("/", trim($uri_parsed['path'], "/\\"));

        if (!empty($params[0])) {
            $action = $params[0];
            if (strpos($action, '.') !== false) {
                $action = 'Main';
            }
        }
        if (!empty($params[1])) {
            $method = $params[1];
        }

        if (!empty($_GET['action'])) {
            $action = $_GET['action'];
        }
        if (!empty($_GET['method'])) {
            $method = $_GET['method'];
        }
        // 多余的参数设置到请求里面
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
}