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
    "import" => array(
    ),
    "components" => array(
        "url_router" => array(
            "class" => "F_Web_Route",
            'rewrite_rules'=>array(
                'index.php?rewrite/index' => 'rewrite/index',
            )
        )
    )
);