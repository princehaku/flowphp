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

    public static $pathAlias = array();

    /**
     * @var F_Core_Log
     */
    private static $_log;

    /**
     * @var F_Core_App
     */
    private static $_app;

    private static $_imports = array();
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
    public static function app() {
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
        if (isset(self::$pathAlias[$alias])) {
            return self::$pathAlias[$alias];
        }
        return false;
    }

    public static function setPathOfAlias($alias, $path) {
        if (empty($path)) {
            unset(self::$pathAlias[$alias]);
        }
        else {
            self::$pathAlias[$alias] = rtrim($path, '\\/');
        }
    }

    /**
     * import一个类文件
     * 要求和
     *
     * @param $alias
     * @param bool $forceInclude
     * @return mixed
     */
    public static function import($alias, $force_include = false) {
        if (isset(self::$_imports[$alias])) {
            return self::$_imports[$alias];
        }
        $alias_arr = explode('.', $alias);

        $base = $alias_arr[0];
        if (isset(self::$pathAlias[$alias_arr[0]])) {
            $base = self::$pathAlias[$alias_arr[0]];
        }
        unset($alias_arr[0]);
        $end_seg = $alias_arr[count($alias_arr)];
        unset($alias_arr[count($alias_arr)]);
        $dir_import = $base . '/' . implode('/', $alias_arr) . '/';

        if ($end_seg != '*') {
            self::$_imports[$alias] = $dir_import . $end_seg . '.php';
            self::$classMap[$end_seg] = self::$_imports[$alias];
        } else {
            // 扫描目录
            if (!is_dir($dir_import)) {
                return;
            }
            $dir_imports_new = array($dir_import);
            while (!empty($dir_imports_new)) {
                $dir_imports = $dir_imports_new;
                $dir_imports_new = array();
                foreach ($dir_imports as $i => $dir_import) {

                    $file_arr = scandir($dir_import);
                    foreach ($file_arr as $file_arr) {
                        $path_info = pathinfo($dir_import . DIRECTORY_SEPARATOR . $file_arr);
                        if (!empty($path_info["filename"]) && $path_info["filename"] != '.'
                            && $path_info["filename"] != '..'
                        ) {
                            if (is_file($dir_import . DIRECTORY_SEPARATOR . $path_info["basename"])) {
                                self::$_imports[$alias] = $dir_import . DIRECTORY_SEPARATOR . $path_info["basename"];
                                self::$classMap[$path_info["filename"]] = self::$_imports[$alias];
                            }
                            // 继续扫描子类
                            if (is_dir($dir_import . DIRECTORY_SEPARATOR . $path_info["basename"])) {
                                $dir_imports_new[] = $dir_import . DIRECTORY_SEPARATOR . $path_info["basename"];
                            }
                        }
                    }
                }
            }
        }

        if ($force_include) {
            include self::$_imports[$alias];
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
        Flow::import("system.core.loader", true);
        Flow::import("system.helper.array", true);
        // 初始化class_loader
        $class_loader = new F_Core_Loader();
        $class_loader->registerAutoLoader();
        // 加载所有配置文件
        $this->_loadcfg();
        // import all
        $imports = isset(self::$cfg["import"]) ? self::$cfg["import"] : array();
        foreach ($imports as $import) {
            Flow::import($import);
        }
        // 初始化所有组件
        $components = isset(self::$cfg["components"]) ? self::$cfg["components"] : array();
        foreach ($components as $name => $config) {
            self::app()->setComponent($name, $config);
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
        $this->app()->run();
    }


    /**
     * 打印页面日志并结束脚本
     *
     */
    public static function showLogs() {
        // 打印日志
        if (DEV_MODE) {
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