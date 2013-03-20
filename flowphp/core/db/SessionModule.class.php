<?php

/** 带session自动的模型类
 * 没有用装饰器实现 使用的时候继承这个类会自动装配session
 * @author princehaku
 * @site http://3haku.net
 */

class SessionModule extends Module {

    /**
     * @overrride
     * @see Module::find()
     */
    public function find($colsname) {
        parent::find($colsname);
        $this->updateSession();
    }

    public function __construct($name) {
        parent::__construct($name);
        $this->unSerializeFromArray($_SESSION[__CLASS__ . $this->tableName]);
    }

    /** 从一个数组解序列化 供SessionModule序列化使用
     *
     */

    final protected function unSerializeFromArray($array) {
        if ($array == null) {
            return $this;
        }
        foreach ($array as $i => $j) {
            $this->$i = $j;
        }
        return $this;
    }

    /** 转换成一个序列化数组 供SessionModule序列化使用
     * 
     */

    private function serializeToArray() {
        $res = array();
        //使用反射获取属性和属性值注入到res数组中
        $rc = new ReflectionClass($this);
        $rcps = $rc->getProperties();
        foreach ($rcps as $i => $j) {
            $pname = $rcps[$i]->getName();
            $res[$pname] = $this->$pname;
        }
        return $res;
    }

    /** 用新数据更新一次session
     * 
     */

    final protected function updateSession() {
        $_SESSION[__CLASS__ . $this->tableName] = $this->serializeToArray();
    }

    /**
     * @overrride
     * @see Module::update()
     */
    public function update() {
        parent::update();
        $this->updateSession();
    }

    /**
     * @overrride
     * @see Module::insert()
     */
    public function insert() {
        parent::insert();
        $this->updateSession();
    }

    /**
     * @overrride
     * @see Module::save()
     */
    public function save() {
        parent::save();
        $this->updateSession();
    }

    /**
     * @overrride
     * @see Module::del()
     */
    public function delete() {
        parent::del();
        $this->updateSession();
    }

    /**
     * @see Module::delall()
     */
    public function delall() {
        parent::delall();
        $this->updateSession();
    }

    /** 魔术函数 实现直接控制属性
     * @override
     * @param $key
     * @return $value
     */

    public function __get($key) {
        return parent::__get($key);
        $this->updateSession();
    }

    /** 得到属性
     * @override
     * @param $key
     * @return $value
     */

    public function get($key) {
        return self::__get($key);
    }

    /** 魔术函数 实现直接设置属性
     * @override
     * @param $key
     * @return $this
     */

    public function __set($key, $value) {
        parent::__set($key, $value);
        $this->updateSession();
    }

    /** 设置属性
     * @override
     * @param $key
     * @param $value
     * @return $this
     */

    public function set($key, $value) {
        return self::__set($key, $value);
    }

}