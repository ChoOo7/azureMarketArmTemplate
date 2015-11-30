<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
require_once("../utils.php");


$commands = array();


$mysqlUser = "cvc";
$mysqlDatabase = $mysqlUser;
$mysqlPassword = substr(md5(uniqid()), 0, 10);
//$mysqlHost = "10.0.0.7";
$mysqlHost = "127.0.0.1";



addPillarInformation($appName, "mysql_password", $mysqlPassword);
addPillarInformation($appName, "mysql_user", $mysqlUser);
addPillarInformation($appName, "mysql_database", $mysqlUser);
addPillarInformation($appName, "mysql_host", $mysqlHost);


$saltCommand = 'salt '.$appName.'-node1 state.sls eztv.deploydatabase';
echo pake_echo($saltCommand);
passthru($saltCommand);


echo "\n";
