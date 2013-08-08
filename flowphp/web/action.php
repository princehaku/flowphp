<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * 默认控制器类
 * F_Web_Action
 */
class F_Web_Action {

    /**
     * @var F_Web_Request
     */
    public $request;

    /**
     * @var F_View_SViewEngine
     */
    private $_view;

    public function setViewEngine($view_engine) {
        $this->_view = $view_engine;
    }

    public function display($view_name = null, $view_data = null) {
        if (empty($view_name)) {
            $view_name = strtolower(get_called_class());
            $view_name = str_replace("controller", "", $view_name);
        }
        //调用模板引擎
        $this->_view->display($view_name, $view_data);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->_view->assign($key, $value);
    }

}
