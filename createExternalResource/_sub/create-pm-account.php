<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

define('CURRENT_DIR', realpath(dirname(__FILE__)));
define('_SUB_DIR', CURRENT_DIR.'/dns/');

require_once('../utils.php');
require_once('./pm/bsPlayerManagerApi.php');
require_once('./create-po-config.php');

$poAccountName = $appName .'-cvc';
$email = $appName .'-cvc@brainsonic.com';
$firstname = 'cvc';
$lastname = $appName;
$username = $poAccountName;
$plainPassword = md5(uniqid());

$playerManagerApi = new bsPlayerManagerApi(
  $playerManagerUserUid,
  $playerManagerAccountUid,
  $playerManagerWebServiceVersion,
  $playerManagerWebServiceUrl
);

//TODO Store this in order to user with deployment

$playerManagerApi->accountCreate($playerManagerSecretKey, $accountName, $packages);
if ($result['status'] !== bsPlayerManagerApi::STATUS_SUCCESS)
{
  pake_error("Error creating PM Account");
  exit(1);
}

$accountUid = $result['data']['guid'];
//pad name (can not exist if error on creation)
$url_pad = "";
if(array_key_exists('url_pad', $result['data']))
{
  $url_pad = $result['data']['url_pad'];
}

$playerManagerApi->userCreate($playerManagerSecretKey, $email, $firstname, $lastname, $accounts, $username, $plainPassword);

if ($result['status'] !== bsPlayerManagerApi::STATUS_SUCCESS)
{
  bsLogger::err('Player Manager: Failed to create user with email '.$email.' and username '.$username);
  return null;
}

$userUid = $result['data']['guid'];
if( ! $userUid)
{
  throw new sfException("Player manager create user account error : ".json_encode($result));
}

$pmUser = bsPlayerManager::userGet("guid", $userUid);

$tvPmUserUid = $pmUser['guid'];
$tvPmUserSecret = $pmUser['secret'];

//We have to store following informations :
$info = array(
  'pad'         => $url_pad,
  'accountUid'  => $accountUid,
  'userUid'     => $tvPmUserUid,
  'userSecret'  => $tvPmUserSecret
);

pake_echo($appName.' : '.serialize($info));

addPillarInformation($appName, "pm_pad", $url_pad);
addPillarInformation($appName, "pm_account_uid", $accountUid);
addPillarInformation($appName, "pm_user_uid", $tvPmUserUid);
addPillarInformation($appName, "pm_user_secret", $tvPmUserSecret);

pake_echo("DONE");



