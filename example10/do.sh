#!/bin/bash

date >> /tmp/date

OLDPWD=$PWD

sudo mkdir -p /provisioning
sudo chmod 777 /provisioning
cd /provisioning

date >> provisioning
echo "PWD : " >> provisioning
echo $PWD  >> provisioning
echo $OLDPWD  >> provisioning


echo "V222 : " >> provisioning

echo "Arg1 : " >> provisioning
echo $1  >> provisioning

echo "Arg2 : " >> provisioning
echo $2  >> provisioning

echo "Arg3 : " >> provisioning
echo $3  >> provisioning


appName=$1
typeOfVm=$2
vmNumber=$3
storageAccountName=$4
storageAccountKey=$5

hostname="${appName}-${typeOfVm}${vmNumber}"


echo -n "${hostname}" > /etc/salt/minion_id
sudo service salt-minion restart

#Wait for salt minion start
sleep 30

#Will ask to salt to deploy solution to this VM
curl -i "http://saltmaster.brainsonic.com/askhighstate.php?hostname=${hostname}" > /root/askInitialInstall



#If salt-minion-id != hostname
  #Configure salt-minion
  #Restart salt-minion
  #Ask (WS-1) for host installation


#if no username cvc
  #call local script to create user / vhost
  #if front1
    #call remote WS-2 in order to create DNS / PAD




#Remote WS-1 :
  #salt-master accept key && highstate
  #if all highstate finished then
    #terminate cluster installation

#Remote WS-2 :
  #Create PAD
  #Create DNS
  #waitUnitil PAD DNS OK then
    #Update DNS


exit 0