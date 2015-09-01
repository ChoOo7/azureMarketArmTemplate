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


#"commandToExecute": "[concat('bash azure-imghlpr.sh ', variables('storageAccountName'),' ',listKeys(concat('Microsoft.Storage/storageAccounts/', variables('storageAccountName')), '2015-05-01-preview').key1,' ', variables('srcImage') )]"