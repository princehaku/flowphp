<?php

/**
 * flowphp基类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class Flow {

    public static $cfg = array();

    public static $params = array();

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
            //初始化基础APP
            Flow::$_app = new F_Core_App();
        }
        return Flow::$_app;
    }

    public static function import($path) {
        $path_arr = explode(".", $path);
        if ($path_arr[0] == "system") {
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

    protected $system_components = array(
        'class_loader' => array(
            "class" => "F_Core_Loader"
        ),
        'web_app' => array(
            'class' => 'F_Web_Application'
        ),
        'console_app' => array(
            'class' => 'F_Cli_Application'
        )
    );

    /**
     * 应用初始化
     * @throws Exception
     */
    public function init($config = array()) {
        if (!defined("DEV_MODE")) {
            define("DEV_MODE", false);
        };
        // 定义路径检测
        if (!defined("APP_PATH") || !defined("DEV_MODE") || !defined("FLOW_PATH")) {
            throw new Exception("No APP_PATH or DEV_MODE or FLOW_PATH Defined");
        }

        $this->import("system.core.app");
        $this->import("system.core.loader");
        // 加载所有配置文件
        $this->_loadcfg($config);
        // 系统默认配置
        foreach ($this->system_components as $name => $config) {
            self::App()->setComponent($name, $config);
        }
        // 初始化class_loader
        $this->App()->class_loader->registerAutoLoader();
        // 初始化所有组件
        $components = isset(self::$cfg["components"]) ? self::$cfg["components"] : array();
        foreach ($components as $name => $config) {
            self::App()->setComponent($name, $config);
        }
    }

    /**
     * 读取和合并配置文件里面的内容
     * @param array $config
     */
    private function _loadcfg($config = array()) {
        // 加载所有配置文件
        self::$cfg = $config;
        // 合并配置文件
        self::$cfg = array_merge(self::$cfg, $this->_includeCfg(APP_PATH . "/config/"));
        // 合并ENV里面的配置
        if (defined("ENV")) {
            self::$cfg = array_merge(self::$cfg, $this->_includeCfg(APP_PATH . "/config/" . ENV . "/"));
        }
    }
    /**
     * 执行
     */
    public function run($run_mode = 'web') {
        // 初始化各种东西
        $this->init();
        if ($run_mode === "cli") {
            $this->App()->console_app->run();
        } else {
            $this->App()->web_app->run();
        }
    }


    /**
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