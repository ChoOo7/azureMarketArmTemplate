<?php
/**
 * bsVideoPublisherServicesSoapClient
 *
 * Video Publisher SOAP client
 *
 * @package   Lib
 * @author    Jérôme Respaut <jerome.respaut@brainsonic.com>
 * @copyright 2010 Brainsonic
 * @version   Release: 2.2
 */
class bsVideoPublisherServicesSoapClient
{
  protected $namespace     = 'http://services.vpo.brainsonic.com/';
  protected $namespacepost = 'http://services.vpo.brainsonic.com/messages';

  protected $wsdlBaseUrl   = '';
  protected $customerID    = '';
  protected $customerKey   = null;
  protected $mode          = null;

  protected $securityClient = null;
  protected $uploadClient   = null;
  protected $jobClient      = null;
  protected $adminService   = null;

  protected $standaloneService  = null;

  protected $fileId = '0';
  protected $errorMsg = '';
  protected $timeout = 180;

  /**
   * Ctor
   *
   * @param string $wsld Base URL
   * @param string $id Customer ID
   * @param string $key Customer Key
   *
   * @return void
   */
  public function __construct($wsld, $id, $key, $mode = null, $thumbsServiceUrl = false)
  {
    $this->wsdlBaseUrl = $wsld;
    $this->customerID  = $id;
    $this->customerKey = new bsVideoPublisherKeyHelper($key);
    $this->mode        = $mode;

    $this->securityClient = new nusoap_client($wsld.'/services/SecurityService.svc?wsdl', 'wsdl');
    $errClient            = $this->securityClient->getError();
    $this->jobClient      = new nusoap_client($wsld.'/services/JobService.svc?wsdl', 'wsdl');
    $errJob               = $this->jobClient->getError();
    $this->uploadClient   = new nusoap_client($wsld.'/services/UploadService.svc?wsdl', 'wsdl');
    $errUpload            = $this->uploadClient->getError();
    $this->adminService   = new nusoap_client($wsld.'/services/AdministrationService.svc?wsdl', 'wsdl');
    $errAdminService      = $this->adminService->getError();

    $this->timeout = 180;

    $thumbsError = false;
    if ($thumbsServiceUrl)
    {
      $this->standaloneService  = new nusoap_client($thumbsServiceUrl, 'wsdl');
      $this->standaloneService->timeout = $this->timeout;
      $thumbsError = $this->standaloneService->getError();
    }

    if ($errClient || $errJob || $errUpload || $errAdminService || $thumbsError)
    {
      $this->errorMsg = $errClient.$errJob.$errUpload.$errAdminService.$thumbsError;
    }
  }

  /**
   * Check credantials of a VPO accont using Security Services
   *
   * @return null|string
   */
  public function checkCredentials()
  {
    if ($this->securityClient != null)
    {
      $nonce = $this->getNonce();
      $response = $this->sendSoapSecureRequest($nonce,
                                               '<checkCredentials>'.md5(mt_rand()).'</checkCredentials>',
                                               'CheckCredentials',
                                               'ISecurityService/CheckCredentials',
                                               $this->securityClient);
      return isset($response['CheckCredentialsResult']) ? $response['CheckCredentialsResult'] : null;
    }
    return null;
  }

  /**
   * Crates a new customer account using Admin Services
   *
   * @param string $customerName
   *
   * @return void
   */
  public function createCustomer($customerName)
  {
    if ($this->adminService != null)
    {
      $nonce    =  $this->getNonce();
      $response =  $this->sendSoapSecureRequest($nonce,
                                                "<CreateCustomerResquest>$customerName</CreateCustomerResquest>",
                                                'CreateCustomer',
                                                'IAdministrationService/CreateCustomer',
                                                $this->adminService);
      return $response;
    }
    return null;
  }


  /**
   * Get the last error message
   *
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMsg;
  }

  /*** ------- Private Methods ------- ***/

  /**
   * Generic code to prepare a secure request, encrypt datas and call the soap action
   *
   * @param string $nonce Unique token for transaction
   * @param string $xmlData Datas to send
   * @param string $soapEnvelope Soap Tag for request
   * @param string $soapAction Soap Action to call
   * @param string $client SOAP client to use for the call
   *
   * @return array
   */
  private function sendSoapSecureRequest($nonce, $xmlData, $soapEnvelope, $soapAction, $client)
  {
    if ($nonce != null)
    {
      $cnonce = bsVideoPublisherCryptoHelper::hmacSha256FromBase64($nonce, $this->customerKey->getHmaKey());
      $data   = bsVideoPublisherCryptoHelper::aesEncryptData($xmlData,
                                                             $this->customerKey->getPrivateKey(),
                                                             $this->customerKey->getIvSalt());
      $data   =  $this->getEnvelopeMessage($soapEnvelope,
                                           array('CNonce'      =>  $cnonce,
                                                 'CustomerId'  =>  $this->customerID,
                                                 'Data'        =>  $data,
                                                 'Mode'        =>  $this->mode,
                                                 'Nonce'       =>  $nonce));

      $mysoapmsg = $client->serializeEnvelope($data, '', array(), 'document', 'literal');
      $result    = $client->send($mysoapmsg, $this->namespace.$soapAction, 0, $this->timeout);

      if ($client->fault || $client->getError() || isset($result['faultcode']))
      {
        $this->errorMsg = print_r($result, true).$client->getError();
        throw new Exception('WebService Error : '.$this->errorMsg);
      }
      return $result;
    }
    else
    {
      throw new Exception('WebService Error : '.$this->errorMsg);
    }
  }

  private function getEnvelopeMessage($soapEnvelope, $values = array())
  {
    $template    = "<$soapEnvelope xmlns=\"".$this->namespace.'">
        <request xmlns:a="'.$this->namespacepost.'" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';

    if (is_array($values))
    {
      foreach($values as $key  =>  $value)
      {
        if ($value)
        {
          $template .=  "<a:$key>$value</a:$key>";
        }
      }
    } else {
      $template .= '<![CDATA['.$values.']]>';
    }
    $template   .=  "</request></$soapEnvelope>";
    return $template;
  }

  /**
   * Get a unique Nonce from VPO Services in order to prepare another secure transaction
   *
   * @return NULL|mixed
   */
  private function getNonce()
  {
    if ($this->securityClient != null)
    {
      $result = $this->securityClient->call('GetNonce');
      if ($this->securityClient->fault || $this->securityClient->getError())
      {
        $this->errorMsg = print_r($result, true).$this->securityClient->getError();
        return null;
      }
      return $result['GetNonceResult'];
    }
    return null;
  }
}
