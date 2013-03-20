<?php

/** 默认控制器类
 * 一般用于模板控制
 * @author princehaku
 * @site http://3haku.net
 */

//载入模版引擎
import("core.view.View");

class FlowAction {

    public function __setViewEngine($__view) {
        $this->__view = $__view;
    }

    //模板资源
    protected $__view;

    public function display($viewname) {
        //调用父类
        $this->__view->display($viewname);
    }

    public function assign($key, $value) {
        // 调用模版
        return $this->__view->assign($key, $value);
    }

    /** 输出成功消息模板
     * 
     * @param string $msg
     * @param string $url
     * @param int $time
     */

    public function success($msg, $url = "", $time = -1) {
        if ($url == "") {
            $url = APP_BASE;
        }
        $this->assign("jmpurl", $url);
        $this->assign("msg", $msg);
        $this->assign("jmptime", $time);

        //模板文件
        $tplfile = getDirPath(C("VIEW_DIR")) . "success.htpl";

        if (file_exists($tplfile)) {
            $this->display("success.htpl");
        } else {
            $tmpview = C("VIEW_DIR");
            C("VIEW_DIR", CORE_BASE . "/view/");
            $this->display("success.htpl");
            C("VIEW_DIR", $tmpview);
        }
        die;
    }

    /** 输出失败消息模板
     *
     * @param string $msg
     * @param string $url
     * @param int $time
     */

    public function error($msg, $url = "", $time = -1) {
        if ($url == "") {
            $url = APP_BASE;
        }
        $this->assign("jmpurl", $url);
        $this->assign("msg", $msg);
        $this->assign("jmptime", $time);

        //模板文件
        $tplfile = getDirPath(C("VIEW_DIR")) . "error.htpl";

        if (file_exists($tplfile)) {
            $this->display("error.htpl");
        } else {
            $tmpview = C("VIEW_DIR");
            C("VIEW_DIR", CORE_BASE . "/view/");
            $this->display("error.htpl");
            C("VIEW_DIR", $tmpview);
        }
        die;
    }

}
