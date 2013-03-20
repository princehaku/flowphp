<?php

/** flowphp基类
 *
 * @author princehaku
 * @site http://3haku.net
 */
import("core.request.Dispatcher");
import("core.request.Request");
import("core.db.DBHelper");
import("core.db.Module");
import("core.db.SessionModule");
import("core.exception.FlowException");
import("core.action.FlowAction");
import("core.action.FlowActiveAction");
import("core.action.FlowStaticAction");

class flowphp {

    /** 执行
     * 
     */
    public function run() {
        header('Content-Type: text/html; charset=utf8');

        $dispatcher = new Dispatcher();
        //加载url分析类 分析url
        if (C("URL_DISPACHER") != "sys") {
            if(import(C("URL_DISPACHER")) == false){
                $this->printAndDie();
            }
            $r = explode(".", C("URL_DISPACHER"));
            $dispatcher = new $r[count($r) - 1] ( );
        }

        $appname = $dispatcher->getAppName();

        $actionname = $dispatcher->getActionName();

        // 初始化请求类
        $request = new Request();
        // 加载对应的控制类
        if (file_exists(APP_BASE . "/action/$appname.class.php")) {
            include_once APP_BASE . "/action/$appname.class.php";
            L()->i("控制文件加载完成$appname.class.php");
        } else {
            L()->e("控制文件不存在$appname.class.php");
            $this->printAndDie();
        }
        if (!class_exists($appname)) {
            L()->e("控制类" . $appname . "不存在");
            $this->printAndDie();
        }
        $action = new $appname();

        $rc = new ReflectionClass($appname);
        if (C("DEBUG") != 0) {
            $beenExtended = $rc->isSubclassOf("FlowStaticAction") || $rc->isSubclassOf("FlowAction") || $rc->isSubclassOf("FlowActiveAction");
            if (!$beenExtended) {
                L()->e("控制文件必须继承FlowAction中的一种");
                $this->printAndDie();
            }
        }

        // 检测方法
        try {
            $rm = new ReflectionMethod($action, $actionname);
        } catch (Exception $e) {
            L()->e("控制类" . $appname . "没有" . $actionname . "方法");
            $this->printAndDie();
        }
        // 检测参数
        if (C("DEBUG") != 0 && isset($rm) && $rm != null && $rm->getNumberOfParameters() != 1) {
            L()->e("控制类" . $appname . "的" . $actionname . "方法必须含有一个request参数");
            $this->printAndDie();
        }
        try {
            $action->__setViewEngine(new View());
            // 执行方法
            $action->$actionname($request);
        } catch (Exception $e) {
            L()->e($e->getMessage());
        }
        // 如果是FlowActiveAction或者FlowStaticAction 不打印日志
        if (!($rc->isSubclassOf("FlowAjaxAction") || $rc->isSubclassOf("FlowStaticAction"))) {

            $this->printAndDie();
        }
    }

    /*     * 打印页面日志并结束脚本
     * 
     */

    public function printAndDie() {
        // 打印日志
        if (C("DEBUG") && C('SHOW_LOG') == 1) {
            $execTime = (microtime(TRUE) - $GLOBALS['_beginTime']);
            L()->i("执行所用时间: " . $execTime);
            L()->i("执行所用内存: " . number_format(memory_get_usage() / 1024, 2) . "kb");
            L()->print_html();
        }
        die;
    }

}