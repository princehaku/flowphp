<?php

define("APP_PATH" , dirname(__FILE__));

define("FLOW_PATH", dirname(dirname(__FILE__)) . "/flowphp/");

include FLOW_PATH . "core.php";

$flow = new Flowphp();
$flow->run();