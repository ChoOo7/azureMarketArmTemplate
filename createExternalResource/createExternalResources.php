<?php

require_once('./utils.php');

<<<<<<< HEAD
=======
$numberOfThread = 1;

>>>>>>> 17e4c4a5bdbbccb0115d02fdb5bab8658b23716d
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

<<<<<<< HEAD

//Log some information in order to debug
file_put_contents("/tmp/create_ext_ress", "\n\n".serialize($informations), FILE_APPEND);
=======
$informations['storageAccountName'] = $storageAccountName;
$informations['storageAccountKey'] = $storageAccountKey;


//Log some information in order to debug
file_put_contents("/tmp/create_ext_ress", "\n\n".serialize($informations), FILE_APPEND);

$portalUser = "admin-cmd";
$portalPassword = substr(md5(uniqid()), 0, 10);

$apiKey = substr(md5(uniqid()), 0, 20);

addPillarInformation($appName, "portal_username", $portalUser);
addPillarInformation($appName, "portal_password", $portalPassword);
addPillarInformation($appName, "portal_apikey", $apiKey);

addPillarInformation($appName, "number_of_front", $numberOfFront);
addPillarInformation($appName, "number_of_node", $numberOfNode);

addPillarInformation($appName, "storage_account_name", $storageAccountName);
addPillarInformation($appName, "storage_account_key", $storageAccountKey);
>>>>>>> 17e4c4a5bdbbccb0115d02fdb5bab8658b23716d


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


<<<<<<< HEAD
doTasksOnMultithread($tasks, 20);

=======
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
>>>>>>> 17e4c4a5bdbbccb0115d02fdb5bab8658b23716d

$command = "php create-dam-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;

<<<<<<< HEAD

//TODO : check if  PAD DNS is OK

$command = "sudo php "._SUB_DIR."create-dns-step2.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
pake_echo($command);
passthru($command);
=======

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

>>>>>>> 17e4c4a5bdbbccb0115d02fdb5bab8658b23716d

//TODO Prevent galera cluster not started

<<<<<<< HEAD
//TODO Prevent galera cluster not started

=======
>>>>>>> 17e4c4a5bdbbccb0115d02fdb5bab8658b23716d

//TODO : Mail the client about his VM
echo "DONE";
