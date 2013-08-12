<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : config.php
 *  Created on : 13-3-21 , 下午5:29
 *  Author     : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

return array(
    "import" => array(
        "application.commands.*"
    ),
    "components" => array(
        "acm" => array(
            "class" => "F_DB_ArManager",
            'connectionString' => 'mysql:host=127.0.0.1;dbname=test;charset=utf8',
            'username' => '',
            'password' => '',
            'charset' => 'utf8'
        )
    )
);