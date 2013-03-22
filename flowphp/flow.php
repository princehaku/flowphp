<?php

/**
 * flowphp基类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class Flow {

    public static $cfg = array();
    /**
     * @var F_Comp_Log
     */
    private static $_log;
    /**
     * @var F_Comp_App
     */
    private static $_app;
    /**
     * 日志
     * 单例模式
     * @return F_Comp_Log
     */
    public static function Log() {
        if (Flow::$_log == null) {
            //初始化日志类
            Flow::$_log = new F_Comp_Log();
        }
        return Flow::$_log;
    }
    /**
     * APP组件
     * 单例模式
     * @return F_Comp_Log
     */
    public static function App() {
        if (Flow::$_app == null) {
            //初始化日志类
            Flow::$_app = new F_Comp_App();
        }
        return Flow::$_app;
    }
    public function import($path) {
        $path_arr = explode(".", $path);
        if ($path_arr[0] == 'core') {
            unset($path_arr[0]);
            $sys_path = implode("/", $path_arr);
            $sys_path = FLOW_PATH . $sys_path . ".php";
        } else {
            $sys_path = implode("/", $path_arr);
            $sys_path = APP_PATH . $sys_path . ".php";
        }
        include $sys_path;
    }
    /**
     * 应用初始化
     * @throws Exception
     */
    public function init() {
        $this->import("core.core.loader");
        spl_autoload_register("F_Core_Loader::autoLoadHandler");
        // 配置异常处理
        if (DEV_MODE) {
            ini_set("display_errors", 1);
            error_reporting(E_ALL);
            set_exception_handler("F_Core_ErrorHandler::exceptionHandler");
            set_error_handler("F_Core_ErrorHandler::errorHandler");
            register_shutdown_function("F_Core_ErrorHandler::fatalShutdownHandler");
        } else {
            error_reporting(0);
            ini_set("display_errors", 0);
        }
        // 关闭zend的php4兼容
        if (ini_get('zend.ze1_compatibility_mode') == true) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        // 定义路径
        if (!defined("APP_PATH") || !defined("DEV_MODE") || !defined("FLOW_PATH")) {
            throw new Exception("No APP_PATH or DEV_MODE or FLOW_PATH Defined");
        }

        @session_start();
        // 加载系统默认配置文件
        $config = include FLOW_PATH . "/config/config.php";
        // 加载程序默认配置文件
        // 加载所有配置文件
        self::$cfg = array_merge(self::$cfg, $config);

        if (file_exists(APP_PATH . "/config/")) {
            $config_files = scandir(APP_PATH . "/config/");
            foreach ($config_files as $i => $config_file) {
                $r = explode(".", $config_file);
                $ext = $r[count($r) - 1];
                if ($ext == "php" && file_exists(APP_PATH . "/config/" . $config_file)) {
                    $config = include APP_PATH . "/config/" . $config_file;
                    // 合并配置文件
                    self::$cfg = array_merge(self::$cfg, $config);
                }
            }
        }
    }
    /**
     * 执行
     */
    public function run() {
        // 初始化各种东西
        $this->init();

        //加载url分析类 分析url
        if (self::$cfg['URL_DISPACHER'] != "sys") {
            $dispatcher = new self::$cfg['URL_DISPACHER'];
        } else {
            $dispatcher = new F_Request_Route();
        }
        $dispatcher->init();
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod();

        // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/action/$action_name.class.php");
        if (file_exists($ac_path)) {
            include_once $ac_path;
            Flow::Log()->info("控制文件加载完成$ac_path");
        } else {
            throw new Exception("控制文件不存在$ac_path");
        }
        $action_name = $action_name . 'Action';
        if (!class_exists($action_name)) {
            throw new Exception("控制类" . $action_name . " 不存在");
        }
        $action = new $action_name();

        // 检测方法
        if (!method_exists($action, $method_name)) {
            throw new Exception("控制类" . $action_name . "没有" . $method_name . "方法");
        }
        $template_engine = self::$cfg['TPL_ENGINE'];
        $action->setViewEngine(new $template_engine());
        try {
            // 初始化请求类
            $request = new F_Request_Request();
            // 执行方法
            $action->$method_name($request);
        } catch (Exception $e) {
            Flow::Log()->error($e->getMessage());
            throw $e;
        }
    }

}