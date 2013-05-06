<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : config.php
 *  Created on : 13-3-21 , ����5:29
 *  Author     : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

return array(
    'base_url' => '',
    "import" => array(
        "module.*",
    ),
    "components" => array(
        "acm" => array(
            "class" => "F_DB_ARManager",
            'connectionString' => 'mysql:host=127.0.0.1;dbname=test;charset=utf8',
            'username' => '',
            'password' => '',
            'charset' => 'utf8'
        )
    )
);