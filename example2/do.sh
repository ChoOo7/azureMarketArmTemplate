#!/bin/bash

date >> /tmp/date
date >> /root/date

date >> /root/provisioning
echo "Arg1 : " >> /root/provisioning
echo $1  >> /root/provisioning

echo "Arg2 : " >> /root/provisioning
echo $2  >> /root/provisioning

echo "Arg2 : " >> /root/provisioning
echo $2  >> /root/provisioning
