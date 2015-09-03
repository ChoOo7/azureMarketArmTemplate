#!/bin/bash

[[ -z "$HOME" || ! -d "$HOME" ]] && { echo 'fixing $HOME'; HOME=/root; }
export HOME

yum install -y epel-release
yum install -y nodejs
yum install -y npm
npm install -g azure-cli
azure config mode arm

diskType="$1"
export AZURE_STORAGE_ACCOUNT="$2"
export AZURE_STORAGE_ACCESS_KEY="$3"

azure storage container create vhds
azure storage blob copy start --source-uri="$4" --dest-container vhds --dest-blob ${diskType}-os-disk-img.vhd
logger -t imghelper "copy $diskType started: $?"

rr=1
while [ $rr -ne 0 ]; do
  sleep 10
  azure storage blob copy show --json img ${diskType}-os-disk-img.vhd | grep '"copyStatus": "success"' >/dev/null
  # "copyStatus": "success",  "copyStatus": "pending"
  rr=$?
done

logger -t imghelper "${diskType} success"
exit 0