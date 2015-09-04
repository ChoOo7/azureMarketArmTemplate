<?php

$secret = $_GET['secret'];
$appName = $_GET['appName'];
$numberOfFront = (int)$_GET['numberOfFront'];
$numberOfNode = (int)$_GET['numberOfNode'];


define('CURRENT_DIR', realpath(dirname(__FILE__)));

#TODO : mail about this script run
//TODO inputs

file_put_contents("/tmp/salt-ask-".$appName, $secret);

$tasks = array();

for($i=1; $i<=$numberOfFront; $i++)
{
  $command = "php ".CURRENT_DIR."/dohighstate.php ".escapeshellarg($appName)." front ".$i." ";
  $tasks[] = $command;
}
for($i=1; $i<=$numberOfNode; $i++)
{
  $command = "php ".CURRENT_DIR."/dohighstate.php ".escapeshellarg($appName)." node ".$i." ";
  $tasks[] = $command;
}

$command = "php create-pad.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;

$command = "php create-dns.php ".escapeshellarg($appName)." ".$numberOfFront." ".$numberOfNode;
$tasks[] = $command;


/*
 *
 * DO NOT WORK for parrallel request ! we have to dynamise pilar variables for galera
 *
 * define galera password for pillar on salt
 *
 * En multi-thread,
 *  N threads pour : on va lancer des highstate sur chacune des machines
 *    Chacun des state va attendre que la machine se déclare aupres du salt-master. Car elle n'est peut être pas encore provisionnée
 *  1 thread pour créer les PADs
 *  1 thread pour créer les DNSs
 *  1 thread pour créer un compte PO
 *  1 thread pour créer un compte DAM
 * Lorsque tous les threads sont terminés :
 *
 * Lancement de nouveaux thread
 *  n thread pour déploiement du code EZTV sur les fronts
 *    if front1 then propel-build-all && tv-create
 *
 * Lorsque tous les threads sont terminés
 *  Test du chargement de l'URL BO (avec présence d'une chaîne)
 *  Mail de rapport
 */
?>#!/bin/bash
echo "Done"
