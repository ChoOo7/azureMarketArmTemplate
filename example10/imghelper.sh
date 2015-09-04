#!/bin/bash

[[ -z "$HOME" || ! -d "$HOME" ]] && { echo 'fixing $HOME'; HOME=/root; }
export HOME

date
logFile='/root/cp.log'
sudo touch $logFile
sudo chmod 777 $logFile
echo "Debut" >> $logFile
date >> $logFile

sudo apt-get install -y --force-yes nodejs-legacy npm
sudo npm install -g azure-cli


azure config mode arm

export AZURE_STORAGE_ACCOUNT="$1"
export AZURE_STORAGE_ACCESS_KEY="$2"
frontSourceUri="$3"
nodeSourceUri="$3"


azure storage container create vhds



#Front disk
diskType="front"
sourceUri="${frontSourceUri}";

azure storage blob copy start --source-uri="${sourceUri}" --dest-container vhds --dest-blob ${diskType}-os-disk-img.vhd
logger -t imghelper "copy $diskType started: $?"

rr=1
while [ $rr -ne 0 ]; do
  sleep 10
  azure storage blob copy show --json img ${diskType}-os-disk-img.vhd | grep '"copyStatus": "success"' >/dev/null
  # "copyStatus": "success",  "copyStatus": "pending"
  rr=$?
done

logger -t imghelper "${diskType} success"



#Node disk
diskType="node"
sourceUri="${nodeSourceUri}";

azure storage blob copy start --source-uri="${sourceUri}" --dest-container vhds --dest-blob ${diskType}-os-disk-img.vhd
logger -t imghelper "copy $diskType started: $?"

rr=1
while [ $rr -ne 0 ]; do
  sleep 10
  azure storage blob copy show --json img ${diskType}-os-disk-img.vhd | grep '"copyStatus": "success"' >/dev/null
  # "copyStatus": "success",  "copyStatus": "pending"
  rr=$?
done

logger -t imghelper "${diskType} success"

echo "Fin" >> $logFile
date >> $logFile


exit 0