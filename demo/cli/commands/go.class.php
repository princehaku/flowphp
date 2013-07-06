<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : go.class.php
 *  Created on : 13-5-6 , 下午11:19
 *  Author     : haku
 *  Blog       : http://3haku.net
 */
class GoCommand extends F_Cli_Command {

    public function actionFetch() {
       echo $this->request->opt('url');
    }
}