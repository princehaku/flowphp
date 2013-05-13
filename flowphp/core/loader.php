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

    public static function autoLoadHandler($class_name) {
        $paths = explode("_", $class_name);

        if (isset($paths[0]) && $paths[0] == "F") {
            unset($paths[0]);
            $lastp = $paths[(count($paths))];
            unset($paths[count($paths)]);
            include FLOW_PATH . strtolower(implode("/", $paths)) . "/" . lcfirst($lastp) . ".php";
        }
    }
}