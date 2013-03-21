<?php

/** flowphp基类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class Flowphp {

    /**
     * 得到日志类
     * 单例模式
     * @return FlowLog
     */
    public static function L() {
        global $L;
        if ($L == null) {
            //初始化日志类
            $L = new Log();
        }
        return $L;
    }

    public function init() {
        import("core.request.Dispatcher");
        import("core.request.Request");
        import("core.db.Module");
        import("core.db.SessionModule");
        import("core.exception.FlowException");
        import("core.action.FlowAction");
        import("core.action.FlowActiveAction");
        import("core.action.FlowStaticAction");

        // 加载系统默认配置文件
        include FLOW_PATH . "/config/config.php";

        $config = array();
        // 加载程序默认配置文件
        // 加载所有配置文件
        if (file_exists(APP_PATH . "/config/")) {
            $config_files = scandir(APP_PATH . "/config/");
            foreach ($config_files as $i => $config_file) {
                $r = explode(".", $config_file);
                $ext = $r[count($r) - 1];
                if ($ext == "php" && file_exists(APP_PATH . "/config/" . $config_file)) {
                    include APP_PATH . "/config/" . $config_file;
                }
            }
            // 合并配置文件
            $config = array_merge($sysconfig, $config);
        }
        // 加载数据库类
        include_once (CORE_BASE . "/core/db/DB.class.php");

        // 加载flowphp公用函数
        $function_files = scandir(CORE_BASE . "/common/");

        foreach ($function_files as $i => $function_file) {
            $r = explode(".", $function_file);
            $ext = $r[count($r) - 1];
            if ($ext == "php" && file_exists(CORE_BASE . "/common/" . $function_file)) {
                include_once (CORE_BASE . "/common/" . $function_file);
            }
        }

        // 加载所有程序公共文件
        if (file_exists(APP_BASE . "/common/")) {
            $function_files = scandir(APP_BASE . "/common/");
            foreach ($function_files as $i => $function_file) {
                $r = explode(".", $function_file);
                $ext = $r[count($r) - 1];
                if ($ext == "php" && file_exists(APP_BASE . "/common/" . $function_file)) {
                    Flowphp::Log()->i("加载文件" . $function_file);
                    include_once (APP_BASE . "/common/" . $function_file);
                }
            }
        }

        // 配置异常处理
        if (C('DEBUG') != "" && C('DEBUG') == 0) {
            error_reporting(0);
            ini_set("display_errors", "0");
            C("LOG_PRI", 0);
        } else {
            ini_set("display_errors", "1");

            error_reporting(E_ALL);

            import("core.exception.FlowErrors");

            set_error_handler("FlowErrors::errorHandler");

            register_shutdown_function("FlowErrors::fatalHandler");
        }
        // 关闭zend的php4兼容
        if (ini_get('zend.ze1_compatibility_mode') == true) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        // 如果没有siteurl 则设置为访问域名
        if (C('SITE_URL') == "") {
            C('SITE_URL', "http://" . $_SERVER['HTTP_HOST']);
            Flowphp::Log()->i("没有在config.php指定SITE_URL");
        }
        // 加载基类
        import("core.base.flowphp");
        // ---------初始化完毕-----------

    }
    /** 执行
     *
     */
    public function run() {
        header('Content-Type: text/html; charset=utf8');

        //加载url分析类 分析url
        if (C("URL_DISPACHER") != null && C("URL_DISPACHER") != "sys") {
            import(C("URL_DISPACHER"));
            $r = explode(".", C("URL_DISPACHER"));
            $dispatcher = new $r[count($r) - 1] ();
        } else {
            $dispatcher = new Dispatcher();
        }
        $appname = $dispatcher->getAppName();

        $actionname = $dispatcher->getActionName();

        // 初始化请求类
        $request = new Request();
        // 加载对应的控制类
        if (file_exists(APP_BASE . "/action/$appname.class.php")) {
            include_once APP_BASE . "/action/$appname.class.php";
            Flowphp::Log()->i("控制文件加载完成$appname.class.php");
        } else {
            Flowphp::Log()->e("控制文件不存在$appname.class.php");
            $this->printAndDie();
        }
        if (!class_exists($appname)) {
            Flowphp::Log()->e("控制类" . $appname . "不存在");
            $this->printAndDie();
        }
        $action = new $appname();

        $rc = new ReflectionClass($appname);
        if (C("DEBUG") != 0) {
            $beenExtended = $rc->isSubclassOf("FlowStaticAction") || $rc->isSubclassOf("FlowAction") || $rc->isSubclassOf("FlowActiveAction");
            if (!$beenExtended) {
                Flowphp::Log()->e("控制文件必须继承FlowAction中的一种");
                $this->printAndDie();
            }
        }

        // 检测方法
        try {
            $rm = new ReflectionMethod($action, $actionname);
        } catch (Exception $e) {
            Flowphp::Log()->e("控制类" . $appname . "没有" . $actionname . "方法");
            $this->printAndDie();
        }
        // 检测参数
        if (C("DEBUG") != 0 && isset($rm) && $rm != null && $rm->getNumberOfParameters() != 1) {
            Flowphp::Log()->e("控制类" . $appname . "的" . $actionname . "方法必须含有一个request参数");
            $this->printAndDie();
        }
        try {
            $action->__setViewEngine(new View());
            // 执行方法
            $action->$actionname($request);
        } catch (Exception $e) {
            Flowphp::Log()->e($e->getMessage());
        }
        // 如果是FlowActiveAction或者FlowStaticAction 不打印日志
        if (!($rc->isSubclassOf("FlowAjaxAction") || $rc->isSubclassOf("FlowStaticAction"))) {

            $this->printAndDie();
        }
    }

    /*
     * 打印页面日志并结束脚本
     *
     */
    public function printAndDie() {
        // 打印日志
        if (C("DEBUG") && C('SHOW_LOG') == 1) {
            $execTime = (microtime(true) - $GLOBALS['_beginTime']);
            Flowphp::Log()->i("执行所用时间: " . $execTime);
            Flowphp::Log()->i("执行所用内存: " . number_format(memory_get_usage() / 1024, 2) . "kb");
            Flowphp::Log()->print_html();
        }
        die;
    }

}