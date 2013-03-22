<?php

/**
 * 默认控制器类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class F_Core_Action {

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
            $view_name = str_replace("action", "", $view_name);
        }
        //调用父类
        $this->_view->display($view_name, $view_data);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->_view->assign($key, $value);
    }

}
