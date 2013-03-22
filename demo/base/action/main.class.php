<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class MainAction extends F_Core_Action {

    public function index() {
        $this->request->getText("a");
        $dbm = Flow::App()->dbm;


        $this->display();
    }
}