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
        "dbm" => array(
            "class" => "F_DB_ActiveRecordManager",
            'dbName' => 'yiqizhai'
        )
    )
);