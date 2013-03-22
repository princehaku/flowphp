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
        $comp = new $config['class']();
        unset($config['class']);
        foreach ($config as $key => $val) {
            $comp->$key = $this->createComponent($val);
        }
        return $comp;
    }

    public function __get($name) {
        return $this->getComponent($name);
    }
}