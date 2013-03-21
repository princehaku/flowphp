<?php
/**
 * Copyright 2012 Etao Inc.
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , ÏÂÎç7:01
 *  Author     : zhongwei.bzw
 */
class MainAction extends F_Core_Action {

    public function index() {
        $this->assign("s", "bbb");
        $this->display();
    }
}