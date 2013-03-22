<?php
/**
 * Copyright 2012 Etao Inc.
 *
 *  FileName : loader.php
 *  Created on : 13-3-21 , 下午11:49
 *  Author     : haku
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