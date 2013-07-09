<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : application.php
 *  Created on : 13-7-6 , 下午5:57
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Cli_Application {

    protected $base_cfg = array(
        "components" => array(
            "request" => array(
                "class" => 'F_Cli_Request'
            ),
            "url_router" => array(
                "class" => "F_Cli_Route"
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

        Flow::App()->request = new F_Cli_Request();
        $dispatcher = new F_Cli_Route();
        $dispatcher->init();
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod(); // 加载对应的控制类
        $ac_path = APP_PATH . strtolower("/commands/");
        if (file_exists($ac_path . "$action_name.class.php")) {
            include $ac_path . "$action_name.class.php";
        } else {
            $files = array_diff(scandir($ac_path), array(".", ".."));
            array_walk($files, function (&$val) {
                $val = str_replace(".class.php", "", $val);
            });
            throw new Exception("命令不存在$ac_path\n支持的命令有" . implode("\n", $files));
        }
        $action_name = $action_name . "Command";
        if (!class_exists($action_name)) {
            throw new Exception("{$action_name} 不存在");
        }
        $action = new $action_name();

        $method_name = "action" . $method_name;
        // 检测方法
        if (!method_exists($action, $method_name)) {
            $methods = get_class_methods($action);
            throw new Exception("{$action_name} 没有{$method_name} 方法\n支持的命令有" . implode("\n", $methods));
        }
        $action->request = Flow::App()->request;
        // 执行方法
        $action->$method_name();
    }
}