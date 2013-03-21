<?php

define("DEV", 1);
define("APP_PATH" , dirname(__FILE__));
define("FLOW_PATH", dirname(dirname(__FILE__)) . "/flowphp/");

include FLOW_PATH . "flow.php";

$flow = new Flow();
$flow->run();