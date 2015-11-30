<?php
/**
 */
class bsVideoPublisherKeyHelper
{
  private $key;
  private $keyBytes;
  private $privateKey;
  private $hmacKey;
  private $ivSalt;

  /**
   * Ctor : initisalization
   *
   * @param string $key Key Source
   *
   * @return void
   */
  public function __construct($key)
  {
    $this->key = $key;
    $this->keyBytes = base64_decode($key);
    $this->privateKey = self::extractKey($this->keyBytes, 0, 16);
    $this->hmacKey = self::extractKey($this->keyBytes, 32, 64);
    $this->ivSalt = self::extractKey($this->keyBytes, 16, 16);
  }

  /**
   * Extract from the key
   *
   * @param string $key Key Source
   * @param int $offset Offset starting extraction
   * @param int $count Number of char to extract
   * @return string
   * 
   */
  public function extractKey($key, $offset, $count)
  {
    $result = array();
    $splittedKey = str_split($key);
    
    for ($i = 0; $i < $count; $i++)
    {
      if(array_key_exists($i + $offset, $splittedKey))
        $result[] = $splittedKey[$i + $offset];
    }
    return implode($result);
  }
  
  
  /**
   * Get the associated Key
   *
   *
   * @return void
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * Get decoded base64 Key
   *
   *
   * @return void
   */
  public function getKeyBytes()
  {
    return $this->keyBytes;
  }

  /**
   * Get private Key
   *
   *
   * @return void
   */
  public function getPrivateKey()
  {
    return $this->privateKey;
  }

  /**
   * Get HMA Key
   *
   *
   * @return void
   */
  public function getHmaKey()
  {
    return $this->hmacKey;
  }

  /**
   * Get Iv Salt for this Key
   *
   *
   * @return void
   */
  public function getIvSalt()
  {
    return $this->ivSalt;
  }
}
