<?php

require_once('./utils.php');

$numberOfThread = 1;

$secret = $_GET['secret'];
$appName = $_GET['appName'];
$numberOfFront = (int)$_GET['numberOfFront'];
$numberOfNode = (int)$_GET['numberOfNode'];
$location = $_GET['location'];

//TODO : transformation location string to location CODE

$informations = array();
$informations['secret'] = $secret;
$informations['appName'] = $appName;
$informations['numberOfFront'] = $numberOfFront;
$informations['numberOfNode'] = $numberOfNode;
$informations['location'] = $location;

$informations['storageAccountName'] = $storageAccountName;
$informations['storageAccountKey'] = $storageAccountKey;


//Log some information in order to debug
file_put_contents("/tmp/create_ext_ress", "\n\n".serialize($informations), FILE_APPEND);

addPillarInformation($appName, "number_of_front", $numberOfFront);
addPillarInformation($appName, "number_of_node", $numberOfNode);

addPillarInformation($appName, "storage_account_name", $storageAccountName);
addPillarInformation($appName, "storage_account_key", $storageAccountKey);


define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/_sub/');

#TODO : mail about this script run
//TODO inputs

//TODO : Placer les pillars MySQL


file_put_contents("/tmp/salt-ask-".$appName, $secret);

$tasks = array();


$command = "sudo php "._SUB_DIR."create-pad.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-dns.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-po-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-pm-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;


for($i=1; $i<=$numberOfFront; $i++)
{
  $command = "sudo php "._SUB_DIR."/dohighstate.php ".escapeshellarg($appName)." front ".$i." ";
  $tasks[] = $command;
}
for($i=1; $i<=$numberOfNode; $i++)
{
  $command = "sudo php "._SUB_DIR."/dohighstate.php ".escapeshellarg($appName)." node ".$i." ";
  $tasks[] = $command;
}


doTasksOnMultithread($tasks, $numberOfThread);


//reboot de front1 et node1 PUIS de tous les autres
$i = 1;
$tasks = array();
$command = "sudo php "._SUB_DIR."/reboot-vm.php ".escapeshellarg($appName)." front ".$i." ";
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."/reboot-vm.php ".escapeshellarg($appName)." node ".$i." ";
$tasks[] = $command;
doTasksOnMultithread($tasks, $numberOfThread);



for($i=2; $i<=$numberOfFront; $i++)
{
  $command = "sudo php "._SUB_DIR."/reboot-vm.php ".escapeshellarg($appName)." front ".$i." ";
  $tasks[] = $command;
}
for($i=2; $i<=$numberOfNode; $i++)
{
  $command = "sudo php "._SUB_DIR."/reboot-vm.php ".escapeshellarg($appName)." node ".$i." ";
  $tasks[] = $command;
}
doTasksOnMultithread($tasks, $numberOfThread);



$command = "sudo php "._SUB_DIR."create-mysql-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
pake_echo($command);
passthru($command);

//Deploy cvc code on front only
$tasks = array();
for($i=1; $i<=$numberOfFront; $i++)
{
  $command = "sudo php "._SUB_DIR."/deploy-cvc.php ".escapeshellarg($appName)." front ".$i." ";
  $tasks[] = $command;
}
doTasksOnMultithread($tasks, $numberOfThread);


//TODO : Wait until PAD DNS is OK

$command = "sudo php "._SUB_DIR."create-dns-step2.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
pake_echo($command);
passthru($command);


//TODO Prevent galera cluster not started


//TODO : Mail the client about his VM
echo "DONE";
