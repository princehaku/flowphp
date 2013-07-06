<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : application.php
 *  Created on : 13-7-6 , 下午5:43
 *  Author     : haku
 *  Blog       : http://3haku.net
 */
class F_Web_Application {

    protected $base_cfg = array(
        "components" => array(
            "request" => array(
                "class" => 'F_Web_Request'
            ),
            "url_router" => array(
                "class" => "F_Web_Route"
            )
        )
    );

    public function init() {
        $components = array_merge(Flow::$cfg, $this->base_cfg);
        // 初始化所有组件
        Flow::App()->setComponent('request', $components["components"]['request']);
        Flow::App()->setComponent('url_router', $components["components"]['url_router']);
    }

    public function run() {
        $dispatcher = Flow::App()->url_router;
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod();
        // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/actions/$action_name.class.php");
        if (file_exists($ac_path)) {
            include $ac_path;
        } else {
            throw new Exception("控制文件不存在$ac_path");
        }
        $action_name = $action_name . "Action";
        if (!class_exists($action_name)) {
            throw new Exception("控制类{$action_name} 不存在");
        }
        $action = new $action_name();
        $method_name = "action" . $method_name;
        // 检测方法
        if (!method_exists($action, $method_name)) {
            throw new Exception("控制类{$action_name} 没有{$method_name} 方法");
        }
        // 初始化action的一些组件
        $action->setViewEngine(new F_View_SViewEngine());
        $request = Flow::App()->request;
        $action->request = $request;
        // 执行方法
        $action->$method_name();
    }


}