<?php

$secret = $_GET['secret'];
$appName = $_GET['appName'];

#TODO : mail about this script run


file_put_contents("/tmp/salt-ask-".$appName, $secret);

/*
 *
 * DO NOT WORK for parrallel request ! we have to dynamise pilar variables for galera
 *
 * define galera password for pillar on salt
 *
 * En multi-thread,
 *  N threads pour : on va lancer des highstate sur chacune des machines
 *  1 thread pour créer les PADs
 *  1 thread pour créer les DNSs
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
