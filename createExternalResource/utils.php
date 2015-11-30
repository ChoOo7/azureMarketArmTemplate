<?php



function pake_error($msg)
{
  echo "\n\nERROR : ".$msg."\n\n";
}

function pake_echo($msg)
{
  echo "\n".$msg."";
}

function getSshPort($hostType, $hostIndex)
{
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
  return $sshPort;
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

function initPillarForApp($appName)
{
  $filename = '/srv/pillar/'.$appName.'/init.sls';
  $topFilename = '/srv/pillar/top.sls';
  if( ! file_exists($filename))
  {
    @mkdir('/srv/pillar/'.$appName.'/');
    file_put_contents($filename, 'eztvconfig:');

    file_put_contents($topFilename , "\n"."  '".$appName."-*':", FILE_APPEND);
    file_put_contents($topFilename , "\n"."    - ".$appName, FILE_APPEND);
  }
  return $filename;
}

function addPillarInformation($appName, $key, $value)
{
  $filename = initPillarForApp($appName);
  $cnt = file_get_contents($filename);
  if(strpos($cnt, $key.':') === false)
  {
    //insert
    file_put_contents($filename, "\n" . '   ' . $key . ': ' . $value, FILE_APPEND);
  }else{
    //update
    $newCnt = "";
    foreach(explode("\n", $cnt) as $line)
    {
      if(strpos($line, $key.":") === false)
      {
        $newCnt.="\n".$line;
      }else{
        $newCnt.="\n".'   '.$key . ': ' . $value;
      }
    }
    file_put_contents($filename, $newCnt);
  }

  pake_echo("refreshing pillar and grains");

  $command = "salt '".$appName."-*' saltutil.refresh_pillar";
  passthru($command);

  $command = "salt '".$appName."-*' saltutil.sync_grains ";
  passthru($command);


}

function getHostUptime($hostname)
{
  $command = 'timeout 10 salt -t 10 ' . $hostname . ' cmd.run "uptime -s"';
  pake_echo($command);
  $output = null;
  exec($command, $output);

  if(empty($output))
  {
    return null;
  }
  $lastLine = array_pop($output);

  $since = strtotime($lastLine);
  if($since == null)
  {
    return null;
  }

  return time() - $since;
}

function saltWayForHostReady($hostname)
{
  while(getHostUptime($hostname) == null)
  {
    sleep(5);
  }
  return true;
}

