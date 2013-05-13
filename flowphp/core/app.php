<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : app.php
 *  Created on : 13-3-22 , 下午3:36
 *  Author     : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

/**
 * app核心应用组件类，用于提供Flow::app()->$alias的单例访问支持
 * 支持从配置生成一个类
 * 并且纳入到app管理中
 *
 * Class F_Core_App
 */
class F_Core_App {
    /**
     * 组件的配置
     * @var array
     */
    private $comp_config = array();

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