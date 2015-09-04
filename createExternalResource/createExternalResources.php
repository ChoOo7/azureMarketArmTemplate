<?php

$secret = $_GET['secret'];
$appName = $_GET['appName'];
$numberOfFront = (int)$_GET['numberOfFront'];
$numberOfNode = (int)$_GET['numberOfNode'];


define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/_sub/');

#TODO : mail about this script run
//TODO inputs

file_put_contents("/tmp/salt-ask-".$appName, $secret);

$tasks = array();


$command = "sudo php "._SUB_DIR."create-pad.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-dns.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-po-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "sudo php "._SUB_DIR."create-playermanager-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
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



function pake_error($msg)
{
  echo "\n\nERROR : ".$msg."\n\n";
}
function pake_echo($msg)
{
  echo "\n".$msg."";
}


function doTasksOnMultithread($commands, $nbChilds)
{
  $taches = array();
  $pid_arr = array();

  for($i=0;$i < $nbChilds;$i++)
  {
    $taches[$i] = array();
  }

  if(function_exists("pcntl_fork"))
  {
    $iProcess = 0;
    $i = 0;
    $k = 0;
    foreach ($commands as $k => $command)
    {
      $taches[$k % $nbChilds][] = $command;
    }
    $iProcess = 0;
    while ($iProcess < $nbChilds)
    {
      pake_echo("forking child " . $iProcess . ' / ' . ($nbChilds - 1));

      $pid = pcntl_fork();

      if ($pid == -1)
      {
        pake_error('could not fork');
        die();
      } else
      {
        if ($pid) // parent
        {
          $pid_arr[$iProcess] = $pid;
        } else // child
        {

          $taches[$iProcess] = array_reverse($taches[$iProcess]);
          foreach ($taches[$iProcess] as $command)
          {
            pake_echo('Lancement de ' . $command);
            passthru($command);
            pake_echo("Processus " . $iProcess . " : FIN d'une iteration");
          }

          pake_echo("Processus " . $iProcess . " : FIN du traitement de ses taches");
          exit(0);//Success
        }
      }
      $iProcess++;
    }

    $nbChilds = count($pid_arr);
    //Attente de la fin des processus fils :
    pake_echo("Processus PERE : Attente de la fin des processus");
    $status = null;
    $i = 0;
    while ($i < $nbChilds)
    {
      $pid = pcntl_waitpid(0, $status);
      pake_echo("Processus PERE : detection de la fin du processus " . $pid . ". " . ($nbChilds - $i - 1) . " processus restant");
      $i++;
    }
    pake_echo("Processus PERE : FIN de l'Attente de la fin des processus");
  }else{
    pake_echo("Librairie pnctl fork non installée, traitement en sequentiel");
    foreach($commands as $i=>$command)
    {
      pake_echo($i.'/'.count($commands));
      pake_echo($command);
      passthru($command);
    }
  }
}


doTasksOnMultithread($tasks, 20);


$command = "php create-dam-account.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;

echo "DONE";
