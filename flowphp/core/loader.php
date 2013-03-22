<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Created on : 13-3-21 , 下午11:49
 *  Blog       : http://3haku.net
 */

class F_Core_Loader {

    public static function autoLoadHandler($class_name) {
        $paths = explode("_", $class_name);

        if (isset($paths[0]) && $paths[0] == "F") {
            unset($paths[0]);
            include FLOW_PATH . strtolower(implode("/", $paths) . ".php");
        }
    }
}