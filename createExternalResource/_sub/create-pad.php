<?php

ini_set('max_execution_time', 3600);

$appName = $argv[1];

//CDNetwork API is not always good so retry is good idea
$tryForEachPad = 5;

require_once('./create-pad-config.php');

$flushUriTemplate = "https://openapi.us.cdnetworks.com/config/rest/pan/site/add?user=".urlencode($cdnetworksUsername)."&pass=".urlencode($cdnetworksPassword)."&pad=%s&origin=%s&copy_settings_from=%s";

$template = 'template-ssl.brainsonic.com';

$try = 0;
while($try <= $tryForEachPad)
{
  $try ++;

  $pad = $appName."-cvc-front-pad.brainsonic.com";
  $origin = "".$appName."-cvc-front-".$appName.".brainsonic.com";
  $flushUrl = sprintf($flushUriTemplate, $pad, $origin, $template);
  $command = 'wget '.$flushUrl;
  passthru($command);

  $pad = $appName."-cvc-services-pad.brainsonic.com";
  $origin = "".$appName."-cvc-services-".$appName.".brainsonic.com";
  $flushUrl = sprintf($flushUriTemplate, $pad, $origin, $template);
  $command = 'wget '.$flushUrl;
  passthru($command);

  $pad = $appName."-cvc-assets-pad.brainsonic.com";
  $origin = "BLOB CORE WINDOWS";//TODO Location true URI
  $flushUrl = sprintf($flushUriTemplate, $pad, $origin, $template);
  $command = 'wget '.$flushUrl;
  passthru($command);

  sleep(10);

}
