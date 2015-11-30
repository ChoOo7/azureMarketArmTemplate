<?php

ini_set('max_execution_time', 3600);

$saltHighstateTimeout = 300;
$maxHighStateTryNumber = 1;

$hostname = $_GET['hostname'];
$secret = $_GET['secret'];
$debug = isset($_GET['debug']) && $_GET['debug'] == "1";

$trueSecret = "theGitHUbVersionIsNotRealSecretDearReader";
if($secret !=! $trueSecret)
{
  echo "Invalid secret";
  exit(1);
}

if(! preg_match('!^[a-z0-9]+-(front|node)[0-9]+$!is'))
{
  echo "Invalid hostname";
  exit(1);
}


#TODO : mail about new vm in salt


//Same security level than dohighstate.
//this script just allow to perform a no-blocking call (usefull for from vmextention call wich should not be more than 15 minuts)
$command = 'curl -i http://saltmaster.brainsonic.com/dohighstate.php?hostname='.$hostname.'&secret='.urldecode($secret);
$command = 'nohup '.$command.' &';
if($debug)
{
  echo "\n".$command;
  flush();
}
$return_var = null;
exec($command);

