<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : Widget.php
 *  Created on : 13-10-9 , 下午4:41
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Web_Widget {

    public function init() {
        $this->setView(Flow::app()->view_engine);
        $this->request = Flow::app()->request;
    }

    /**
     * @var F_Web_Request
     */
    public $request;

    /**
     * @var F_View_SViewEngine
     */
    public $view;

    public function setView($view_engine) {
        $this->view = $view_engine;
    }

    public function display($view_name = null, $view_data = null) {
        if (empty($view_name)) {
            $view_name = strtolower(get_called_class());
            $view_name = str_replace("controller", "", $view_name);
        }
        // 调用模板引擎
        $this->view->display($view_name, $view_data);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->view->assign($key, $value);
    }

}