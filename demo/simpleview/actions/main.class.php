<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class MainAction extends F_Core_Action {

    public function actionIndex() {
        $this->assign("s", "bbb");
        $this->assign("arr",
            array("b", "wa")
        );
        $this->assign("arrarr",
            array(
                0 => array("k" => "b", "c" => "2013-01-11"),
                1 => array("k" => "b", "c" => "waB"))
        );
        $this->assign("arrdeep",
            array(
                0 => array(
                    "k" => "b",
                    "c" => array(
                        "k" => "b",
                        "c" => "deep_waA")
                ),
                1 => array(
                    "k" => "b",
                    "c" => array(
                        "k" => "b",
                        "c" => "deep_waB")
                ))
        );

        $this->display();
    }
}