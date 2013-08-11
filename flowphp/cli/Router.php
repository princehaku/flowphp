<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * command分发器
 * 分发到对应的action和method
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Cli_Router {

    protected $action;

    protected $method;

    public function init() {
        $this->_routeCli();
    }

    public function getAction() {
        return $this->action;
    }

    public function getMethod() {
        return $this->method;
    }

    private function _routeCli() {
        // 拆开
        $this->action = $_SERVER['argv'][1];

        $this->method = $_SERVER['argv'][2];
    }

}