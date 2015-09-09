<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/dns/');

$commands = array();

$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-front-".$appName." front.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;

$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-services-".$appName." services.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;

$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-admin-".$appName." admin.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;


$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-front-pad front.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;

$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-services-pad services.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;

$command = "php "._SUB_DIR."/create_dns_cname.php ".$appName."-cvc-assets-pad  assets.cvc.".$appName.".".".$appName.".".brainsonic.com";
$commands[] = $command;


$command = "php "._SUB_DIR."/push_to_prod.php";
$commands[] = $command;

foreach($commands as $command)
{
  echo "\n\n".$command."\n";
  passthru($command);
  echo "\n___________\n";
}
echo "\nDONE\n";



