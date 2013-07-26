<?php

/**
 * flowphp基类
 *
 * @author princehaku
 * @site http://3haku.net
 */

class Flow {

    public static $cfg = array();

    public static $classMap = array();

    /**
     * @var F_Core_Log
     */
    private static $_log;

    /**
     * @var F_Core_App
     */
    private static $_app;

    private static $_imports = array();

    private static $_pathAliases = array();
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
            if (PHP_SAPI === "cli") {
                Flow::$_app = new F_Cli_App();
            } else {
                Flow::$_app = new F_Web_App();
            }
            Flow::$_app->init();
        }
        return Flow::$_app;
    }

    public static function getPathOfAlias($alias) {
        if (isset(self::$_pathAliases[$alias]))
            return self::$_pathAliases[$alias];
        return false;
    }

    public static function setPathOfAlias($alias, $path) {
        if (empty($path))
            unset(self::$_pathAliases[$alias]);
        else
            self::$_pathAliases[$alias] = rtrim($path, '\\/');
    }

    public static function import($path, $include_now = false) {
        if (isset(self::$_imports[$path])) {
            return self::$_imports[$path];
        }
        $alias_arr = explode('.', $path);
        $base = self::getPathOfAlias($alias_arr[0]);
        unset($alias_arr[0]);
        $end_seg = $alias_arr[count($alias_arr)];
        unset($alias_arr[count($alias_arr)]);
        $dir_import = $base . '/' . implode('/', $alias_arr) . '/';

        if ($end_seg != '*') {
            self::$_imports[$path] = $dir_import . $end_seg . '.php';
            self::$classMap[$end_seg] = self::$_imports[$path];
        } else {
            // 扫描目录
            if (!file_exists($dir_import)) {
                return;
            }
            $file_arr = scandir($dir_import);
            foreach ($file_arr as $file_arr) {
                $path_info = (pathinfo($file_arr));
                if (!empty($path_info["filename"]) && $path_info["filename"] != '.'
                    && $path_info["filename"] != '..'
                ) {
                    self::$_imports[$path] = $dir_import . $path_info["basename"];
                    self::$classMap[$path_info["filename"]] = self::$_imports[$path];
                }
            }
        }
        if ($include_now) {
            include self::$_imports[$path];
        }
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
                    $cfg = F_Helper_Array::MergeArray($cfg, $config);
                }
            }
        }
        return $cfg;
    }

    /**
     * 应用初始化
     * @throws Exception
     */
    public function init() {
        if (!defined("DEV_MODE")) {
            define("DEV_MODE", false);
        };
        // 定义路径检测
        if (!defined("APP_PATH") || !defined("FLOW_PATH")) {
            throw new Exception("No APP_PATH or FLOW_PATH Defined");
        }
        $this->setPathOfAlias('system', FLOW_PATH);
        $this->setPathOfAlias('application', APP_PATH);
        $this->import("system.core.loader", true);
        $this->import("system.helper.array", true);
        // 初始化class_loader
        $class_loader = new F_Core_Loader();
        $class_loader->registerAutoLoader();
        // 加载所有配置文件
        $this->_loadcfg();
        // 初始化所有组件
        $components = isset(self::$cfg["components"]) ? self::$cfg["components"] : array();
        foreach ($components as $name => $config) {
            self::App()->setComponent($name, $config);
        }
    }

    public function setEnv($env) {
        self::$cfg['BASE_ENV'] = $env;
    }

    /**
     * 读取和合并配置文件里面的内容
     * @param array $config
     */
    private function _loadcfg() {
        // 合并配置文件
        self::$cfg = F_Helper_Array::MergeArray(self::$cfg, $this->_includeCfg(APP_PATH . "/config/"));
        // 合并ENV里面的配置
        if (!empty(self::$cfg['BASE_ENV'])) {
            self::$cfg = F_Helper_Array::MergeArray(self::$cfg, $this->_includeCfg(APP_PATH .
            "/config/" . self::$cfg['BASE_ENV'] . "/"));
        }
    }

    /**
     * 执行
     */
    public function run() {
        // 初始化各种东西
        $this->init();
        $this->App()->run();
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