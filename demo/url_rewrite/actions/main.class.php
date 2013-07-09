<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class MainAction extends F_Web_Action {

    public function actionIndex() {
        header('Location:' . Flow::App()->basePath . '/test/b');

        $this->display();
    }
}