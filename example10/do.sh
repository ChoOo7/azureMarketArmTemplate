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
numberOfFront=$6
numberOfNode=$7
location=$8

hostname="${appName}-${typeOfVm}${vmNumber}"


#Store storage key for future usage
echo -n $storageAccountName > /root/.storageAccountName
echo -n $storageAccountKey > /root/.storageAccountKey

chmod 700 /root/.storageAccountName
chmod 700 /root/.storageAccountKey

sudo service salt-minion stop

echo -n "${hostname}" > /etc/salt/minion_id
rm -f /etc/salt/pki/minion/minion_master.pub
rm -f /etc/salt/pki/minion/minion.pem
rm -f /etc/salt/pki/minion/minion.pub

#Install salt-minion if it's not already installed
sudo apt-get install -y --force-yes salt-minion

sudo service salt-minion start

#Wait for salt minion start
sleep 30

#Will ask to salt to deploy solution to this VM
#curl -i "http://saltmaster.brainsonic.com/askhighstate.php?hostname=${hostname}" > /root/askInitialInstall

if [ "${typeOfVm}${vmNumber}" -e "front-1" ] then
  echo "isFront1"
  curl -i "http://saltmaster.brainsonic.com/createExternalResources.php?secret=${secret}&appName=${appName}&numberOfFront=${numberOfFront}&numberOfNode=${numberOfNode}&location=${location}"
fi

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