<?php

require_once('../utils.php');


$minDeployStateIteration = 3;

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

saltWayForHostReady($hostname);

//deploy global code
$return_var = 1;
$tryNumber = 0;
while($tryNumber <= $minDeployStateIteration)
{
  $command = 'salt -t ' . $saltTimeout . ' ' . $hostname . ' state.sls eztv.deploycode';
  pake_echo($command);
  passthru($command);

  $tryNumber++;
}

//create cvc

$tryNumber = 0;
while($tryNumber <= $minDeployStateIteration)
{
  $command = 'salt -t ' . $saltTimeout . ' ' . $hostname . ' state.sls eztv.deploycvc';
  pake_echo($command);
  passthru($command);

  $tryNumber++;
}

echo "\n";



