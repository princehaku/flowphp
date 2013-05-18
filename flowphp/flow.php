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
     * @var F_Core_Log
     */
    private static $_log;
    /**
     * @var F_Core_App
     */
    private static $_app;
    /**
     * 日志
     * 单例模式
     * @return F_Core_Log
     */
    public static function Log() {
        if (Flow::$_log == null) {
            //初始化日志类
            Flow::$_log = new F_Core_Log();
        }
        return Flow::$_log;
    }
    /**
     * APP组件
     * 单例模式
     *
     * @return F_Core_App
     */
    public static function App() {
        if (Flow::$_app == null) {
            //初始化日志类
            Flow::$_app = new F_Core_App();
        }
        return Flow::$_app;
    }

    public static function import($path) {
        $path_arr = explode(".", $path);
        if ($path_arr[0] == "core") {
            unset($path_arr[0]);
            $sys_path = implode("/", $path_arr);
            $sys_path = FLOW_PATH . "/" . $sys_path . ".php";
        } else {
            $sys_path = implode("/", $path_arr);
            $sys_path = APP_PATH . "/" . $sys_path . ".php";
        }
        include $sys_path;

        return $path_arr[count($path_arr) - 1];
    }

    /**
     * 包含一个目录下的所有配置文件 $cfg_dir 已/结尾
     * @param $cfg_dir
     * @return array
     */
    private function _includeCfg($cfg_dir) {
        $cfg = array();
        if (file_exists($cfg_dir)) {
            $config_files = scandir($cfg_dir);
            foreach ($config_files as $i => $config_file) {
                $r = explode(".", $config_file);
                $ext = $r[count($r) - 1];
                if ($ext == "php" && file_exists($cfg_dir . $config_file)) {
                    $config = include $cfg_dir . $config_file;
                    // 合并配置文件
                    $cfg = array_merge($cfg, $config);
                }
            }
        }
        return $cfg;
    }
    /**
     * 应用初始化
     * @throws Exception
     */
    public function init($config = array()) {
        $this->import("core.core.loader");
        spl_autoload_register("F_Core_Loader::autoLoadHandler");
        if (!defined("DEV_MODE")) {
            define("DEV_MODE", false);
        }
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
        // 定义路径
        if (!defined("APP_PATH") || !defined("DEV_MODE") || !defined("FLOW_PATH")) {
            throw new Exception("No APP_PATH or DEV_MODE or FLOW_PATH Defined");
        }

        session_start();
        // 系统默认配置
        $config_default = array(

            "components" => array(
                "file_varcache" => array(
                    "class" => "F_Cache_File"
                )

            )
        );
        // 加载所有配置文件
        self::$cfg = array_merge(self::$cfg, $config_default);
        // 加载所有配置文件
        self::$cfg = array_merge(self::$cfg, $config);
        // 合并配置文件
        self::$cfg = array_merge(self::$cfg, $this->_includeCfg(APP_PATH . "/config/"));
        // 合并ENV里面的配置
        if (defined("ENV")) {
            self::$cfg = array_merge(self::$cfg, $this->_includeCfg(APP_PATH . "/config/" . ENV . "/"));
        }

        $components = self::$cfg["components"];
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
        if (PHP_SAPI === "cli") {
            $this->_runCli();
        } else {
            $this->_runWeb();
        }
    }

    private function _runCli() {
        $this->App()->request = new F_Cli_Request();
        $dispatcher = new F_Cli_Route();
        $dispatcher->init();
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod(); // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/commands/");
        if (file_exists($ac_path . "$action_name.class.php")) {
            include $ac_path . "$action_name.class.php";
        } else {
            $files = array_diff(scandir($ac_path), array(".", ".."));
            array_walk($files, function (&$val) {
                $val = str_replace(".class.php", "", $val);
            });
            throw new Exception("命令不存在$ac_path\n支持的命令有" . implode("\n", $files));
        }
        $action_name = $action_name . "Command";
        if (!class_exists($action_name)) {
            throw new Exception("{$action_name} 不存在");
        }
        $action = new $action_name();

        $method_name = "action" . $method_name;
        // 检测方法
        if (!method_exists($action, $method_name)) {
            $methods = get_class_methods($action);
            throw new Exception("{$action_name} 没有{$method_name} 方法\n支持的命令有" . implode("\n", $methods));
        }
        $action->request = $this->App()->request;
        // 执行方法
        $action->$method_name();
    }

    private function _runWeb() {
        $this->App()->request = new F_Web_Request();
        // 加载url分析类 分析url
        if (isset(self::$cfg["url_dispacher"])) {
            $dispatcher = new self::$cfg["url_dispacher"];
        } else {
            $dispatcher = new F_Web_Route();
        }
        $dispatcher->init();
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod();

        // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/actions/$action_name.class.php");
        if (file_exists($ac_path)) {
            include $ac_path;
        } else {
            throw new Exception("控制文件不存在$ac_path");
        }
        $action_name = $action_name . "Action";
        if (!class_exists($action_name)) {
            throw new Exception("控制类{$action_name} 不存在");
        }
        $action = new $action_name();
        $method_name = "action" . $method_name;
        // 检测方法
        if (!method_exists($action, $method_name)) {
            throw new Exception("控制类{$action_name} 没有{$method_name} 方法");
        }
        // 初始化action的一些组件
        $action->setViewEngine(new F_View_SViewEngine());
        $request = $this->App()->request;
        $action->request = $request;
        // 执行方法
        $action->$method_name();
    }

    /*
     * 打印页面日志并结束脚本
     *
     */
    public static function showLogs() {
        // 打印日志
        if (DEV_MODE) {
            if (!headers_sent()) {
                header("Content-Type:text/html;charset=utf-8");
            }
            if (PHP_SAPI == 'cli') {
                $errors = FLow::Log()->getDatas();
                foreach ($errors as $message) {
                    echo implode(" ", $message);
                    echo "\n";
                }
            } else {
                echo Flow::Log()->getHTML();
            }
        }
    }
}