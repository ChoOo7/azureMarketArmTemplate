<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/dns/');

$commands = array();


$username = "cvc";
$mysqlPassword = substr(md5(uniqid()), 0, 10);


$command = 'CREATE DATABASE '.$username.'';
$commands[] = $command;

$command = "CREATE USER '".$username."'@'%' IDENTIFIED BY '".$mysqlPassword."'";
$commands[] = $command;

$command = "GRANT ALL PRIVILEGES ON ".$username.".* TO ".$username."@'%'";
$commands[] = $command;

$command = "FLUSH PRIVILEGES";
$commands[] = $command;

foreach($commands as $command)
{
  $execCommand = 'echo "'.$command.'" | mysql --defaults-file=/etc/mysql/debian.cnf --default-character-set=utf8"';
  passthru($execCommand);
}

addPillarInformation($appName, "mysql_password", $mysqlPassword);
