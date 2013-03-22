<?php
/**
 * Copyright 2012 Etao Inc.
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class MainAction extends F_Core_Action {

    public function index($request) {
        var_dump($request);
        $this->assign("s", "bbb");
        $this->display();
    }
}