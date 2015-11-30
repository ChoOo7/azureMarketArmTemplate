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


#TODO : mail about new vm in salt - in doing mode


$command = 'echo y | salt-key -a '.$hostname;
if($debug)
{
  echo "\n".$command;
  flush();
}
$return_var = null;
passthru($command, $return_var);
echo "\nReturn var : ".$return_var;


$return_var = 1;
$tryNumber = 0;
while($return_var != 0 && $tryNumber < $maxHighStateTryNumber)
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
}


#TODO : if node, state galera

#TODO : add this host to /root/exploit/fabric/harmonize_keys/servers_list
#TODO : deploy SSH Key on the VM
#TODO : restart VM

#TODO : mail about vm creation status
#TODO : Mail the client about his VM



