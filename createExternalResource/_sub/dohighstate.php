<?php

ini_set('max_execution_time', 3600);

$saltHighstateTimeout = 300;

$minHighStateTryNumber = 3;
$maxHighStateTryNumber = 10;

$appName = $argv[1];
$hostType = $argv[2];
$hostIndex = $argv[3];
$sshPass = $argv[4];
$debug = isset($argv[5]) && $argv[5] == "1";

$hostname = $appName.'-'.$hostType.$hostIndex;

if(! preg_match('!^[a-z0-9]+-(front|node)[0-9]+$!is', $hostname))
{
  echo "Invalid hostname";
  exit(1);
}


#TODO : mail about new vm in salt - in doing mode



$sshPort = 11122;
switch($hostType)
{
  case 'front';
    $sshPort = 11122 + $hostIndex - 1;
    break;
  case 'node';
    $sshPort = 11130 + $hostIndex - 1;
    break;
  case 'worker';
    $sshPort = 11140 + $hostIndex - 1;
    break;
}



$saltKeyAdded = false;
while(true)
{

  $command = 'echo y | salt-key --list=accepted | grep ' . $hostname . ' | wc -l';
  if ($debug)
  {
    echo "\n" . $command;
    flush();
  }
  $output = null;
  exec($command, $output);

  $output = trim(implode('', array_map("trim", $output)));
  echo "\noutput : " . $output;
  if ($output == "1")
  {
    $saltKeyAdded = true;
    break;
  }

  sleep(2);

  $command = 'echo y | salt-key -a ' . $hostname;
  if ($debug)
  {
    echo "\n" . $command;
    flush();
  }
  $return_var = null;
  passthru($command, $return_var);
  echo "\nReturn var : " . $return_var;
}

//SAlt key ok -> host is UP :)

#Deploy root ssh keys
#We do it before highstate, because in highstate we are going to disable password login to VM
$sourceKey = "/root/.ssh/authorized_keys";
$rootUsername = "brainsonicadmin";
$sshHostname = $appName.'-cvc-brainsonic.westeurope.';//TODO
$command = 'sshpass -p \''.$sshPass.'\' scp -P '.$sshPort.' ' . $sourceKey . ' brainsonicadmin@' . $sshHostname . ':'.$sourceKey;
if ($debug)
{
  echo "\n" . $command;
  flush();
}
$return_var = null;
passthru($command, $return_var);




//Do salt HighState
$return_var = 1;
$tryNumber = 0;
while(($tryNumber <= $minHighStateTryNumber) || ($return_var != 0 && $tryNumber < $maxHighStateTryNumber))
{
  $command = 'salt -t ' . $saltHighstateTimeout . ' ' . $hostname . ' state.highstate';
  if ($debug)
  {
    echo "\n" . $command;
    flush();
  }
  $return_var = null;
  passthru($command, $return_var);
  echo "\nReturn var : " . $return_var;

  $tryNumber++;
}


#TODO : add this host to /root/exploit/fabric/harmonize_keys/servers_list

$sleep = 1;
if($hostType == "node")
{
  //try to prevent an host which will not start because all VMs started at the same time
  $sleep = ($hostIndex - 1) * 120;
}

echo "\nSleep ".$sleep." before reboot\n";
sleep($sleep);

#Reboot target host (= restart all services)
$command = 'salt -t ' . $saltHighstateTimeout . ' ' . $hostname . ' cmd.run reboot';
if ($debug)
{
  echo "\n" . $command;
  flush();
}
$return_var = null;
passthru($command, $return_var);
echo "\nReturn var : " . $return_var;


#TODO : mail about vm creation status




