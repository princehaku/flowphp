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
        $nodes = $dbm->query("select * from entry limit 10;");
        $node = $nodes[0];
        $dbm->save($node);

        $this->display();
    }
}