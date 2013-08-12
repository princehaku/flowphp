<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : App.php
 *  Created on : 13-7-6 , 下午5:57
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Cli_App extends F_Core_App {

    protected $base_components = array(
        "components" => array(
            "request" => array(
                "class" => 'F_Cli_Request'
            ),
            "url_router" => array(
                "class" => "F_Cli_Router"
            )
        )
    );

    public function init() {
        $components = F_Helper_Array::MergeArray($this->base_components, Flow::$cfg);
        // 初始化所有组件
        $this->setComponent('request', $components["components"]['request']);
        $this->setComponent('url_router', $components["components"]['url_router']);
    }

    public function run() {
        $dispatcher = Flow::app()->getComponent('url_router');
        $action_name = $dispatcher->getAction();
        $method_name = $dispatcher->getMethod();

        $action_name = ucfirst($action_name) . "Command";

        // 检测是否存在
        if (empty(FLow::$classMap[$action_name])) {
            if (DEV_MODE) {
                header("HTTP/1.1 404 Not Found");
                throw new Exception("Controller $action_name Not Found");
            } else {
                header("HTTP/1.1 404 Not Found");
                die;
            }
        }

        $action = Flow::app()->createComponent(array(
            'class' => $action_name,
            'viewEngine' => array(
                'class' => 'F_View_SViewEngine'
            ),
            'request' => Flow::app()->getComponent('request')
        ));
        $action->beforeCommand();
        $method_name = "action" . $method_name;
        // 检测方法
        if (!method_exists($action, $method_name)) {
            if (DEV_MODE) {
                header("HTTP/1.1 404 Not Found");
                throw new Exception("Controller {$action_name} has No {$method_name} Method");
            } else {
                header("HTTP/1.1 404 Not Found");
                die;
            }
        }
        // 执行方法
        $action->$method_name();
    }
}