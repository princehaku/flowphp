<?php

/**
 * 默认控制器类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class F_Core_Action {

    public function setViewEngine($viewengine) {
        $this->_view = $viewengine;
    }

    //模板资源
    private $_view;

    public function display($viewname) {
        //调用父类
        $this->_view->display($viewname);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->_view->assign($key, $value);
    }

}
