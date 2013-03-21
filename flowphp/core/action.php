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

    public function setViewEngine($viewengine) {
        $this->_view = $viewengine;
    }

    public function display($viewname = null) {
        if (empty($viewname)) {
            $viewname = strtolower(get_called_class());
            $viewname = str_replace("action", "", $viewname);
        }
        //调用父类
        $this->_view->display($viewname);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->_view->assign($key, $value);
    }

}
