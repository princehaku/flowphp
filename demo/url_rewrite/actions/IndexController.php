<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : main.php
 *  Created on : 13-3-21 , 下午7:01
 *  Author     : zhongwei.bzw
 */
class IndexController extends F_Web_Controller {

    public function actionIndex() {
        header('Location:' . Flow::app()->basePath . '/test/b');

        $this->display();
    }
}