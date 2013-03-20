<?php

/** url分发器
 * 支持rewrite,pathinfo和普通get方式
 * @author princehaku
 * @site http://3haku.net
 */

class Dispatcher {

    protected $appName;
    protected $actionName;

    public function __construct() {

        $params = array();

        $app = 'Main';
        $action = 'index';

        // 是否使用path info
        if (C("PATH_INFO") == 1) {
            $url = $_SERVER['PATH_INFO'];
        } else {
            $url = $_SERVER['REQUEST_URI'];
            $url = basename($url);
            if (isset($_GET['action'])) {
                $app = $_GET['action'];
                $url = '';
            }
            if (isset($_GET['method'])) {
                $action = $_GET['method'];
                $url = '';
            }
        }
        $url = str_replace(APP_PATH, "", $url);
        // 拆开?
        $url = explode("?", $url);
        
        $url = $url[0];
        // 按斜杠分拆
        $params = explode("/", $url);

        if (isset($params[1]) && $params[1] != '') {
            $app = $params[1];
        }
        if (isset($params[2]) && $params[2] != '') {
            $action = $params[2];
        }
        // 设置到请求里面
        foreach ($params as $i => $j) {
            if ($i % 2 == 0 && $i != 0) {
                $_GET[$params[$i - 1]] = $params[$i];
            }
        }
        $this->appName = htmlspecialchars_deep($app);
        $this->actionName = htmlspecialchars_deep($action);

        if ($this->appName == $this->actionName) {
            throw new FlowException("Action和其属性名相同,无法创建");
        }
    }

    public function getAppName() {
        return $this->appName;
    }

    public function getActionName() {
        return $this->actionName;
    }

}