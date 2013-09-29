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
                $paths[0] = Flow::getPathOfAlias($paths[0]);
                $lastp = $paths[(count($paths) - 1)];
                unset($paths[count($paths) - 1]);
                foreach ($paths as $i => $path_seg) {
                    if ($i != 0 ) {
                        $paths[$i] = strtolower($path_seg);
                    }
                }
                $file_path = implode(DIRECTORY_SEPARATOR, $paths) .
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
    }
}