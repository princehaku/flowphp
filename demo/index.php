<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName : index.php
 *  Created on : 13-3-19 , 下午7:01
 *  Author     : zhongwei.bzw
 */

define("DEV_MODE", 1);
define("APP_PATH" , dirname(__FILE__));
define("FLOW_PATH", dirname(dirname(__FILE__)) . "/flowphp/");

include FLOW_PATH . "flow.php";

$flow = new Flow();
$flow->run();