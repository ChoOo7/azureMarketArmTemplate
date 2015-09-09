<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/dns/');

require_once('./create-po-config.php');

$poAccountName = $appName .'-cvc';

$vpoClient = new bsVideoPublisherServicesSoapClient($vpoBaseUrl, null, $poKey, 'Admin');


$response = $vpoClient->createCustomer($poAccountName);

$adminKey = new bsVideoPublisherKeyHelper($poKey);

$result = bsVideoPublisherCryptoHelper::aesDecryptData(
  $response['CreateCustomerResult']['Data'],
  $adminKey->getPrivateKey(),
  $adminKey->getIvSalt()
);

$xml = @simplexml_load_string($result);

if (!$xml)
{
  pake_error("Error creating po account for ".$appName);
}

$poCustomerId = (string)$xml->CustomerGuid;
$poCustomerKey = (string)$xml->ApiKey;


$info = array(
  'po_customer_id'         => $poCustomerId,
  'po_customer_key'        => $poCustomerKey
);

pake_echo($appName.' : '.serialize($info));

addPillarInformation($appName, "po_customer_id", $poCustomerId);
addPillarInformation($appName, "po_customer_key", $poCustomerKey);

echo "\nDONE\n";



