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
    private $_compConfig = array();

    public function setComponent($name, $config) {
        $this->_compConfig[$name] = $config;
    }

    /**
     * 获取一个组件
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getComponent($name) {
        if (empty($this->_compConfig[$name])) {
            throw new Exception("组件{$name}不存在");
        }
        if (is_object($this->_compConfig[$name])) {
            return $this->_compConfig[$name];
        }
        if (is_array($this->_compConfig[$name])) {
            $this->_compConfig[$name] = $this->createComponent($this->_compConfig[$name]);
            return $this->_compConfig[$name];
        }
    }

    /**
     * 依据配置文件数组创建一个类
     * array('class'=>'xxxxx','import'=>'lib.go', 'a'=>'b')
     * 其他将被注入到参数中
     *
     * @param $config
     * @return mixed
     * @throws Exception
     */
    public function createComponent($config) {
        if (!is_array($config)) {
            return $config;
        }
        if (empty($config['class'])) {
            return $config;
        }
        if (!empty($config['import'])) {
            foreach($config['import'] as $imt) {
                Flow::import($imt);
            }
        }
        $class_name = $config['class'];
        $comp = new $class_name();
        unset($config['class']);
        unset($config['import']);
        foreach ($config as $key => $val) {
            $comp->$key = $this->createComponent($val);
        }
        $comp->init();
        return $comp;
    }

    /**
     * @param $name
     * @return mixed
     * @see $name
     */
    public function __get($name) {
        return $this->getComponent($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->_compConfig[$name]);
    }
}