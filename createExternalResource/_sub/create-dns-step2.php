<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/dns/');

$commands = array();

$command = "php "._SUB_DIR."/update_dns_cname.php ".$appName."-cvc-front-pad ".$appName."-cvc-front-pad.brainsonic.com.cdngc.net";
$commands[] = $command;

$command = "php "._SUB_DIR."/update_dns_cname.php ".$appName."-cvc-services-pad ".$appName."-cvc-front-pad.brainsonic.com.cdngc.net";
$commands[] = $command;

$command = "php "._SUB_DIR."/update_dns_cname.php ".$appName."-cvc-assets-pad ".$appName."-cvc-front-pad.brainsonic.com.cdngc.net";
$commands[] = $command;




