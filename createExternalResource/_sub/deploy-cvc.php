<?php

ini_set('max_execution_time', 3600);

$minDeployStateIteration = 3;
$maxDeployStateIteration = 6;
$saltTimeout = 300;

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


$sshPort = getSshPort($hostType, $hostIndex);


//Do salt HighState
$return_var = 1;
$tryNumber = 0;
while(($tryNumber <= $minDeployStateIteration) || ($return_var != 0 && $tryNumber < $maxDeployStateIteration))
{
  $command = 'salt -t ' . $saltTimeout . ' ' . $hostname . ' state.sls eztv.deploycode';
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




