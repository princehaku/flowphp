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
     *
     * @return F_Comp_App
     */
    public static function App() {
        if (Flow::$_app == null) {
            //初始化日志类
            Flow::$_app = new F_Comp_App();
        }
        return Flow::$_app;
    }

    public static function import($path) {
        $path_arr = explode(".", $path);
        if ($path_arr[0] == 'core') {
            unset($path_arr[0]);
            $sys_path = implode("/", $path_arr);
            $sys_path = FLOW_PATH . "/" . $sys_path . ".php";
        } else {
            $sys_path = implode("/", $path_arr);
            $sys_path = APP_PATH . "/" . $sys_path . ".php";
        }
        include $sys_path;

        return $path_arr[count($path_arr) -1];
    }
    /**
     * 应用初始化
     * @throws Exception
     */
    public function init($config) {
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
        // 系统默认配置
        $config_default = array(
            // 缓存目录
            "appcache_dir" => APP_PATH . "/appcache/",
            // 强制注销REQUEST
            "unset_reqs" => 1,
            // URL 分发器
            "url_dispacher" => "sys",
            // 跟踪错误来源
            "trace_error" => 1,

            "components" => array(
                'file_varcache' => array(
                    'class' => 'F_CACHE_File'
                )

            )
        );
        // 加载所有配置文件
        self::$cfg = array_merge(self::$cfg, $config_default);
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
        $components = self::$cfg['components'];
        foreach ($components as $name => $config) {
            self::App()->setComponent($name, $config);
        }
    }
    /**
     * 执行
     */
    public function run() {
        // 初始化各种东西
        $this->init();

        //加载url分析类 分析url
        if (self::$cfg['url_dispacher'] != "sys") {
            $dispatcher = new self::$cfg['url_dispacher'];
        } else {
            $dispatcher = new F_Request_Route();
        }
        $dispatcher->init();
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod();

        // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/action/$action_name.class.php");
        if (file_exists($ac_path)) {
            include $ac_path;
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
        // 初始化action的一些组件
        $action->setViewEngine(new F_View_SViewEngine());
        $request = new F_Request_Request();
        $action->request = $request;
        // 执行方法
        $action->$method_name();
    }

}