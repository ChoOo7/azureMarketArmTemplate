<?php
require_once('../utils.php');

$minDeployStateIteration = 3;
$maxDeployStateIteration = 6;
$saltTimeout = 300;

$appName = $argv[1];
$hostType = $argv[2];
$hostIndex = $argv[3];

$hostname = $appName.'-'.$hostType.$hostIndex;

if(! preg_match('!^[a-z0-9]+-(front|node)[0-9]+$!is', $hostname))
{
  echo "Invalid hostname";
  exit(1);
}


$initialUptime = getHostUptime($hostname);
pake_echo("Intial uptime:".$initialUptime);


#Reboot target host (= restart all services)
$command = 'timeout 10 salt -t 10 ' . $hostname . ' cmd.run reboot';
pake_echo($command);
passthru($command);


sleep(10);
//wait for host reboot
$time = null;
while($time == null || $time > $initialUptime)
{
  $time = getHostUptime($hostname);
  pake_echo("time:" . $time);
}

if($hostType == "node")
{

  pake_echo("wait for node syncronisation");

  $nodeIsSynced = false;
  while( ! $nodeIsSynced)
  {
    $command = 'timeout 10 salt -t 10 ' . $hostname . ' cmd.run "/usr/bin/clustercheck"';
    pake_echo($command);
    $output = null;
    exec($command, $output);
    if ( ! empty($output))
    {
      $tmp = implode(' ', $output);
      pake_echo($tmp);
      if(strpos($tmp, "is synced") !== false)
      {
        $nodeIsSynced = true;
      }
    }
    if( ! $nodeIsSynced)
    {
      sleep(5);
    }
  }
}

