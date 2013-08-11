<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * 默认控制器类
 * F_Web_Controller
 */
class F_Web_Controller {

    public function init() {

    }

    public function beforeController() {

    }
    /**
     * @var F_Web_Request
     */
    public $request;

    /**
     * @var F_View_SViewEngine
     */
    public $viewEngine;

    public function setViewEngine($view_engine) {
        $this->viewEngine = $view_engine;
    }

    public function displayView($view_name = null, $view_data = null) {
        // 调用模板引擎
        $this->viewEngine->displayView($view_name, $view_data);
    }

    public function displayLayout($layout_name = null, $view_data = null) {
        // 调用模板引擎
        $this->viewEngine->displayLayout($layout_name, $view_data);
    }

    public function display($view_name = null, $view_data = null) {
        if (empty($view_name)) {
            $view_name = strtolower(get_called_class());
            $view_name = str_replace("controller", "", $view_name);
        }
        // 调用模板引擎
        $this->viewEngine->display($view_name, $view_data);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->viewEngine->assign($key, $value);
    }

}
