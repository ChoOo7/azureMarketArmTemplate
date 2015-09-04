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
nodeSourceUri="$4"

echo "creating or update container"

echo "creating"
azure storage container create --permission Blob vhds

echo "updating"
azure storage container set --permission Blob --container vhds

echo "container created or updated"

#Front disk
diskType="front"
sourceUri="${frontSourceUri}";

echo "sourceUri $frontSourceUri"
echo "storage account $1"
echo "storage key $2"

echo "starting copy"
azure storage blob copy start --source-uri="${sourceUri}" --dest-container vhds --dest-blob ${diskType}-os-disk-img.vhd
echo "copy started"
logger -t imghelper "copy $diskType started: $?"

rr=1
while [ $rr -ne 0 ]; do
  sleep 10
  echo -n "checking state : "
  azure storage blob copy show --json vhds ${diskType}-os-disk-img.vhd | grep copyProgress
  azure storage blob copy show --json vhds ${diskType}-os-disk-img.vhd | grep '"copyStatus": "success"' >/dev/null
  # "copyStatus": "success",  "copyStatus": "pending"
  rr=$?
  echo "state checked"
done

echo "front disk copied"
logger -t imghelper "${diskType} success"


date
#Node disk
diskType="node"
sourceUri="${nodeSourceUri}";

echo "starting copy"
azure storage blob copy start --source-uri="${sourceUri}" --dest-container vhds --dest-blob ${diskType}-os-disk-img.vhd
echo "copy started"
logger -t imghelper "copy $diskType started: $?"

rr=1
while [ $rr -ne 0 ]; do
  sleep 10
  echo -n "checking state : "
  azure storage blob copy show --json vhds ${diskType}-os-disk-img.vhd | grep copyProgress
  azure storage blob copy show --json vhds ${diskType}-os-disk-img.vhd | grep '"copyStatus": "success"' >/dev/null
  # "copyStatus": "success",  "copyStatus": "pending"
  rr=$?
done

echo "copy node done"
date

logger -t imghelper "${diskType} success"

echo "Fin" >> $logFile
date >> $logFile


exit 0