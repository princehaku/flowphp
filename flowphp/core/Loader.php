<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Created on : 13-3-21 , 下午11:49
 *  Blog       : http://3haku.net
 */

/**
 * autoloader 用于自动加载类
 * Class F_Core_Loader
 */
class F_Core_Loader {

    public function init() {
    }

    /**
     * 两种加载模式
     * @param $class_name
     * @throws Exception
     */
    public static function autoLoadHandler($class_name) {
        if (isset(Flow::$classMap[$class_name])) {
            include Flow::$classMap[$class_name];
            return;
        }

        if (strpos($class_name, "_") !== false) {
            $paths = explode("_", $class_name);

            if (isset($paths[0])) {
                if ($paths[0] == "F") {
                    $paths[0] = FLOW_PATH;
                }
                if ($paths[0] == "APP") {
                    $paths[0] = APP_PATH;
                }
                $lastp = $paths[(count($paths) - 1)];
                unset($paths[count($paths) - 1]);
                $file_path = strtolower(implode(DIRECTORY_SEPARATOR, $paths)) .
                    DIRECTORY_SEPARATOR . $lastp . ".php";

                if (DEV_MODE) {
                    if (!file_exists($file_path)) {
                        throw new Exception("Class $class_name LoadError" . PHP_EOL .
                        "File $file_path Not Existed");
                    }
                }
                include $file_path;
            }
        }
    }

    public function registerAutoLoader() {
        spl_autoload_register("F_Core_Loader::autoLoadHandler");
        // 配置异常处理
        if (DEV_MODE) {
            ini_set("display_errors", 1);
            error_reporting(E_ALL);
            set_exception_handler("F_Core_ErrorHandler::exceptionHandler");
            set_error_handler("F_Core_ErrorHandler::errorHandler");
            register_shutdown_function("F_Core_ErrorHandler::fatalShutdownHandler");
        }
    }
}