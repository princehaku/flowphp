<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class TestAction extends F_Web_Action {

    public function actionB() {
        echo "!!!不应该进这个类!!!";
        die();
    }
}