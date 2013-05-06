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
        $entry_acm = Flow::App()->acm->table("entry");

        $comicnodes = $entry_acm->where("cat='cartoon'")->order("publish_date desc, `dsr_score` desc")->limit("30")->findall();
        $movienodes = $entry_acm->where("cat='movie'")->order("publish_date desc, `dsr_score` desc")->limit("30")->findall();

        $this->assign("comicnodes", $comicnodes);
        $this->assign("movienodes", $movienodes);
        $this->display();
    }
}