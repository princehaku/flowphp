<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : rewrite.class.php
 *  Created on : 13-7-10 , 上午12:03
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class RewriteController extends F_Web_Controller {

    public function actionIndex() {
        echo "All Get Various <br />";
        var_dump($_GET);
        die();
    }

}