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

    public static function autoLoadHandler($class_name) {
        $paths = explode("_", $class_name);

        if (isset($paths[0]) && $paths[0] == "F") {
            unset($paths[0]);
            $lastp = $paths[(count($paths))];
            unset($paths[count($paths)]);
            $file_path = FLOW_PATH . strtolower(implode("/", $paths)) . "/" . lcfirst($lastp) . ".php";
            if (DEV_MODE) {
                if (!file_exists($file_path)) {
                    throw new Exception("类 $class_name 加载失败" . PHP_EOL .
                    "文件 $file_path 不存在");
                }
            }
            include $file_path;
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
        } else {
            error_reporting(0);
            ini_set("display_errors", 0);
        }
    }
}