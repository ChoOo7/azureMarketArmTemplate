#!/bin/bash

sudo mkdir -p /provisioning
sudo chmod 777 /provisioning
cd /provisioning

date >> provisioning
echo "PWD : " >> provisioning
echo $PWD  >> provisioning

echo "Arg1 : " >> provisioning
echo $1  >> provisioning

echo "Arg2 : " >> provisioning
echo $2  >> provisioning

echo "Arg2 : " >> provisioning
echo $2  >> provisioning
