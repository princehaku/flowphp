<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : app.php
 *  Created on : 13-3-22 , 下午3:36
 *  Author     : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

class F_Comp_App {
    private $comp_config = array();
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
                foreach($errors as $message) {
                    echo implode(" ", $message);
                    echo "\n";
                }
            } else {
                echo Flow::Log()->getHTML();
            }
        }
    }

    public function setComponent($name, $config) {
        $this->comp_config[$name] = $config;
    }

    public function getComponent($name) {
        if (empty($this->comp_config[$name])) {
            throw new Exception("组件{$name}不存在");
        }
        if (is_object($this->comp_config[$name])) {
            return $this->comp_config[$name];
        }
        if (is_array($this->comp_config[$name])) {
            $this->comp_config[$name] = $this->createComponent($this->comp_config[$name]);
            return $this->comp_config[$name];
        }
    }

    public function createComponent($config) {
        if (!is_array($config)) {
            return $config;
        }
        if (empty($config['class'])) {
            throw new Exception("组件config必须有class");
        }
        if (!empty($config['import'])) {
            Flow::import($config['import']);
        }
        $class_name = $config['class'];
        $comp = new $class_name();
        unset($config['class']);
        foreach ($config as $key => $val) {
            $comp->$key = $this->createComponent($val);
        }
        $comp->init();
        return $comp;
    }

    public function __get($name) {
        return $this->getComponent($name);
    }
}