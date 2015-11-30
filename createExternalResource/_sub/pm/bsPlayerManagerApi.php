<?php

class bsPlayerManagerApi
{
  const API_DOMAIN_DEFAULT = 'http://playermanager.brainsonic.com';
  const API_VERSION_DEFAULT = '2';
  const BASE_URL_FORMAT = '{api_domain}/rest/{userUid}';


  // Status code & message
  const STATUS_SUCCESS              = 'success';
  const STATUS_FAIL                 = 'fail';
  const STATUS_CODE_OK              = 200;
  const STATUS_CODE_BAD_REQUEST     = 400;
  const STATUS_CODE_UNAUTHORIZED    = 401;
  const STATUS_CODE_FORBIDDEN       = 403;
  const STATUS_CODE_NOT_FOUND       = 404;
  const STATUS_CODE_SERVER_ERROR    = 500;

  private static $apiStatusMessages = array(
    self::STATUS_CODE_OK            => 'OK',
    self::STATUS_CODE_BAD_REQUEST   => 'Bad Request',
    self::STATUS_CODE_UNAUTHORIZED  => 'Unauthorized',
    self::STATUS_CODE_FORBIDDEN     => 'Forbidden',
    self::STATUS_CODE_NOT_FOUND     => 'Not Found',
    self::STATUS_CODE_SERVER_ERROR  => 'Internal Server Error'
  );

  // Response format
  const REPONSE_FORMAT_JSON         = 'json';
  const REPONSE_FORMAT_XML          = 'xml';


  // API Actions
  const ACTION_USER_AUTHENTICATE      = 'userAuthenticate';
  const ACTION_ACCOUNT_CREATE         = 'accountCreate';
  const ACTION_USER_CREATE            = 'userCreate';
  const ACTION_USER_UPDATE            = 'userUpdate';
  const ACTION_USER_GET               = 'userGet';
  const ACTION_PACKAGE_LIST           = 'packageList';
  const ACTION_PLAYER_LIST            = 'playerList';
  const ACTION_PLAYER_GET             = 'playerGet';
  const ACTION_PLAYER_CREATE          = 'playerCreate';
  const ACTION_PLAYER_UPDATE          = 'playerUpdate';
  const ACTION_PLAYER_DELETE          = 'playerDelete';
  const ACTION_PLAYER_ARCHIVE         = 'playerArchive';
  const ACTION_PLAYER_PROPERTIES      = 'playerProperties';

  private static $apiActions = array(
      self::ACTION_USER_AUTHENTICATE  => '/user/{accountUid}/authenticate.{format}',
      self::ACTION_ACCOUNT_CREATE     => '/account/create.{format}',
      self::ACTION_PACKAGE_LIST       => '/{accountUid}/package/all.{format}',
      self::ACTION_PLAYER_LIST        => '/{accountUid}/player/all.{format}',
      self::ACTION_PLAYER_GET         => '/{accountUid}/player/{playerUid}/get.{format}',
      self::ACTION_PLAYER_CREATE      => '/{accountUid}/player/create.{format}',
      self::ACTION_PLAYER_UPDATE      => '/{accountUid}/player/{playerUid}/update.{format}',
      self::ACTION_PLAYER_DELETE      => '/{accountUid}/player/{playerUid}/delete.{format}',
      self::ACTION_PLAYER_ARCHIVE     => '/{accountUid}/player/{playerUid}/download.{format}',
      self::ACTION_PLAYER_PROPERTIES  => '/{accountUid}/player/{playerUid}/properties.{format}',


      self::ACTION_USER_CREATE        => '/user/create.{format}',
      self::ACTION_USER_UPDATE        => '/user/{updatedUserUid}/update.{format}',
      self::ACTION_USER_GET           => '/user/get.{format}'

  );

  protected $apiDomain;
  protected $apiVersion;
  protected $accountUid;
  protected $userUid;
  protected $baseApiUrl;
  protected $canUseCache = true;
  
  protected static $__cache = array();

  /**
   *
   * @param string $userUid
   * @param string $accountUid
   * @param string $version
   * @param string $apiDomain
   */
  public function __construct($userUid, $accountUid, $version = self::API_VERSION_DEFAULT, $apiDomain = self::API_DOMAIN_DEFAULT)
  {
    $this->userUid = $userUid;
    $this->accountUid = $accountUid;
    $this->apiVersion = $version;
    $this->apiDomain = $apiDomain;
    $this->updateBaseApiUrl();
  }

  public function userAuthenticate($secret, $format = self::REPONSE_FORMAT_JSON, $redirectUrl = null, $mode = null)
  {
    $action = self::$apiActions[self::ACTION_USER_AUTHENTICATE];
    $action = str_replace('{format}', $format, $action);

    $options = array();
    $options["secret"] = $secret;
    if($redirectUrl)
    {
      $options['redirectUrl'] = $redirectUrl;
    }
    if($mode)
    {
      $options['mode'] = $mode;
    }

    return $this->callPlayerManagerAPI($action, $options);
  }

  public function accountCreate($secret, $accountName, $packages = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = self::$apiActions[self::ACTION_ACCOUNT_CREATE];
    
    $action = str_replace('{userUid}', $this->userUid, $action);
    $action = str_replace('{format}', $format, $action);

    $options = array();
    $postOptions = array();
    $postOptions["secret"] = $secret;
    $postOptions["rest_account"] = array();
    $postOptions["rest_account"]["enabled"] = "1";
    $postOptions["rest_account"]["name"] = $accountName;

    //On aurait put faire une simple affectation, mais est ready à retraiter les données
    foreach($packages as $id=>$packageNames)
    {
      //if( ! array_key_exists("packages", $postOptions["account_create"]))
      if( ! array_key_exists("packages", $postOptions["rest_account"]))
      {
        //$postOptions["account_create"]["packages"] = array();
        $postOptions["rest_account"]["packages"] = array();
      }
      //$postOptions["account_create"]["packages"][$id] = $packageNames;
      $postOptions["rest_account"]["packages"][$id] = $packageNames;
    }
  
    $postOptions = http_build_query($postOptions, '', '&');
    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }
  

  public function userCreate($secret, $email, $firstname, $lastname, $accounts, $username, $plainPassword, $format = self::REPONSE_FORMAT_JSON)
  {
    $action = self::$apiActions[self::ACTION_USER_CREATE];
    $action = str_replace('{userUid}', $this->userUid, $action);
    $action = str_replace('{format}', $format, $action);
  
    $options = array();
    $options["secret"] = $secret;
  
    $postOptions = array();
    $postOptions["rest_user_registration"] = array();
    $postOptions["rest_user_registration"]["email"] = $email;
    $postOptions["rest_user_registration"]["firstname"] = $firstname;
    $postOptions["rest_user_registration"]["lastname"] = $lastname;
    $postOptions["rest_user_registration"]["username"] = $username;
    $postOptions["rest_user_registration"]["plainPassword"] = $plainPassword;

    $postOptions["rest_user_registration"]["accounts"] = $accounts;
  
  
    $postOptions = http_build_query($postOptions, '', '&');
    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }
  

  public function userUpdate($secret, $userUid, $email, $firstname, $lastname, $accounts, $username, $plainPassword, $format = self::REPONSE_FORMAT_JSON)
  {
    $action = self::$apiActions[self::ACTION_USER_UPDATE];
    $action = str_replace('{userUid}', $this->userUid, $action);
    $action = str_replace('{updatedUserUid}', $userUid, $action);
    $action = str_replace('{format}', $format, $action);
    
  
    $options = array();
    $options["secret"] = $secret;
  
    $postOptions = array();
    $postOptions["rest_user_registration"] = array();
    $postOptions["rest_user_registration"]["email"] = $email;
    $postOptions["rest_user_registration"]["firstname"] = $firstname;
    $postOptions["rest_user_registration"]["lastname"] = $lastname;
    $postOptions["rest_user_registration"]["username"] = $username;
    if($plainPassword)
    {
      $postOptions["rest_user_registration"]["plainPassword"] = $plainPassword;
    }

    $postOptions["rest_user_registration"]["accounts"] = $accounts;
  
  
    $postOptions = http_build_query($postOptions, '', '&');
    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }
  

  public function userGet($secret, $fieldName, $fieldValue, $format = self::REPONSE_FORMAT_JSON)
  {
    $action = self::$apiActions[self::ACTION_USER_GET];
    $action = str_replace('{userUid}', $this->userUid, $action);
    $action = str_replace('{format}', $format, $action);
  
    $options = array();
    $options[$fieldName] = $fieldValue;
    $options['secret'] = $secret;
    
    return $this->callPlayerManagerAPI($action, $options);
  }

  /**
   *
   * @param array $options
   * @param string $format json|xml
   * @return type
   */
  public function playerList($options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace('{format}', $format, self::$apiActions[self::ACTION_PLAYER_LIST]);

    return $this->callPlayerManagerAPI($action, $options);
  }

  public function packageList($options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace('{format}', $format, self::$apiActions[self::ACTION_PACKAGE_LIST]);

    return $this->callPlayerManagerAPI($action, $options);
  }

  /**
   *
   * @param string $playerUid
   * @param array $options
   * @param string $format json|xml
   * @return type
   */
  public function playerGet($playerUid, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{playerUid}', '{format}'),
      array($playerUid, $format),
      self::$apiActions[self::ACTION_PLAYER_GET]
    );

    return $this->callPlayerManagerAPI($action, $options);
  }

  /**
   *
   * @param string $playerUid
   * @param array $options
   * @param string $format json|xml
   * @return type
   */
  public function playerCreate($secret, $title, $package, $enabled = 1, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{format}'),
      array($format),
      self::$apiActions[self::ACTION_PLAYER_CREATE]
    );

    $postOptions = array();
    $postOptions["secret"] = $secret;
    $postOptions["rest_player"] = array();
    $postOptions["rest_player"]["name"] = $title;
    $postOptions["rest_player"]["package"] = $package;
    $postOptions["rest_player"]["enabled"] = $enabled ? true : false;

    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }


  public function playerUpdate($secret, $playerUid, $title, $package, $enabled = 1, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{playerUid}', '{format}'),
      array($playerUid, $format),
      self::$apiActions[self::ACTION_PLAYER_UPDATE]
    );

    $postOptions = array();
    $postOptions["secret"] = $secret;
    $postOptions["rest_player"] = array();
    $postOptions["rest_player"]["name"] = $title;
    $postOptions["rest_player"]["package"] = $package;
    $postOptions["rest_player"]["enabled"] = $enabled ? true : false;

    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }

  public function playerDelete($secret, $playerUid, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{playerUid}', '{format}'),
      array($playerUid, $format),
      self::$apiActions[self::ACTION_PLAYER_DELETE]
    );

    $postOptions = array();
    $postOptions['secret'] = $secret;

    return $this->callPlayerManagerAPI($action, $options, $postOptions);
  }

  /**
   *
   * @param string $playerUid
   * @param string $options
   * @return type
   */
  public function playerArchive($playerUid, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{playerUid}', '{format}'),
      array($playerUid, $format),
      self::$apiActions[self::ACTION_PLAYER_ARCHIVE]
    );

    return $this->callPlayerManagerAPI($action, $options);
  }

  /**
   *
   * @param string $playerUid
   * @param array $options
   * @param string $format
   * @return type
   */
  public function playerProperties($playerUid, $category = null, $options = array(), $format = self::REPONSE_FORMAT_JSON)
  {
    $action = str_replace(
      array('{playerUid}', '{format}'),
      array($playerUid, $format),
      self::$apiActions[self::ACTION_PLAYER_PROPERTIES]
    );
    if ($category)
    {
      $options['category'] = $category;
    }
    
    if(array_key_exists("category", $options))
    {
      if($options["category"] == "dimensions")
      {
        $options["category"] = "skin_dimensions";
      }
      if($options["category"] == "features")
      {
        $options["category"] = "features_other";
      }
      
    }

    return $this->callPlayerManagerAPI($action, $options);
  }

  public function setAccountUid($accountUid)
  {
    $this->accountUid = $accountUid;
  }

  public function setUserUid($userUid)
  {
    $this->userUid = $userUid;
    $this->updateBaseApiUrl();
  }

  public function setApiDomain($apiDomain)
  {
    $this->apiDomain = $apiDomain;
    $this->updateBaseApiUrl();
  }

  public function setApiVersion($apiVersion)
  {
    $this->apiVersion = $apiVersion;
    $this->updateBaseApiUrl();
  }

  public function canUseCache()
  {
    return $this->canUseCache;
  }

  public function setUseCache($val = true)
  {
    $this->canUseCache = $val;
  }

  protected function callPlayerManagerAPI($action, $options = array(), $postOptions = array())
  {
    try
    {
//      if (SF_DEBUG)
//        $options['api_debug_mode'] = true;


      if( ! bsCache::readCacheIsActivated() && empty($postOptions))
      {
        //PM not ready to accept this parameter for the moment
        $options["_"] = uniqid();
      }

      $options = http_build_query($options, '', '&');
      $options = !empty($options) ? '?'.$options : '';
      $url = $this->updateApiUrl($this->baseApiUrl.$action).$options;

      $curlOptions = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER=>array('X-API-Version: '.$this->apiVersion)
      );
      $__cache_key = sha1(json_encode($curlOptions));

      if (!empty($postOptions))
      {
        if(is_array($postOptions))
        {
          $postOptions = http_build_query($postOptions, null, '&');
        }
        $curlOptions[CURLOPT_POST] = true;
        $curlOptions[CURLOPT_POSTFIELDS] = $postOptions;
      }elseif($this->canUseCache()){
        
        if(array_key_exists($__cache_key, self::$__cache))
        {
          return self::$__cache[$__cache_key];
        }
      }
      


      $curlResource = curl_init();
      curl_setopt_array($curlResource, $curlOptions);
      $response = curl_exec($curlResource);
      $status = curl_getinfo($curlResource);
      $error = curl_error($curlResource);
      curl_close($curlResource);

      $objectResponse = json_decode($response, true);
      $statusCode = $status ? $status['http_code'] : 999;

      if (!$status || $statusCode != self::STATUS_CODE_OK)
      {
        $error = empty($error) && isset(self::$apiStatusMessages[$statusCode]) ? self::$apiStatusMessages[$statusCode] : $error;

        return array('status' => self::STATUS_FAIL, 'code' => $statusCode, 'data' => $error);
      }
      
      $result = array('status' => self::STATUS_SUCCESS, 'code' => $statusCode, 'data' => $objectResponse);
      self::$__cache[$__cache_key] = $result;

      return $result;
    }
    catch (Exception $e)
    {
      return array('status' => self::STATUS_FAIL, 'code' => $e->getCode(), 'data' => $e->getMessage());
    }
  }

  protected function updateBaseApiUrl()
  {
    $apiDomain = /*SF_DEBUG ? $this->apiDomain.'/app_dev.php' : */$this->apiDomain;
    $this->baseApiUrl = $this->updateApiUrl(self::BASE_URL_FORMAT);
  }

  protected function updateApiUrl($url)
  {
    $apiDomain = /*SF_DEBUG ? $this->apiDomain.'/app_dev.php' : */$this->apiDomain;
    return str_replace(
      array('{api_domain}', '{version}', '{accountUid}', '{userUid}'),
      array($apiDomain, $this->apiVersion, $this->accountUid, $this->userUid),
      $url
    );
  }
}
