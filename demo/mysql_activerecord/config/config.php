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
    'base_url' => '/mysql_activerecord',
    "import" => array(
        "module.*",
        "actions.*",
    ),
    "components" => array(
        "acf" => array(
            "class" => "F_DB_ArFactory",
            'connectionString' => 'mysql:host=127.0.0.1;dbname=test;charset=utf8',
            'username' => '',
            'password' => '',
            'charset' => 'utf8'
        )
    )
);