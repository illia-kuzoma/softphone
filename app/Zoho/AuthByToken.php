<?php

namespace zoho;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\ZohoOAuth;

include_once "Config.php";
include_once "Auth.php";
include_once "Log.php";

class AuthByToken extends Auth
{
  private static $scopes = 'ZohoCRM.modules.ALL,ZohoCRM.users.ALL,ZohoCRM.org.ALL,ZohoCRM.settings.ALL';
  private static $grantToken = '1000.b1d32d8167e5ce6e822eba87559ddba2.7c6265c5dbb57b91e067df8f3b04b140';
  private static $refreshToken = '';
  public $token_file_name = '';

  public function __construct()
  {
    new Config([
      "redirect_uri"=>self::redirect_uri,
      "currentUserEmail"=>self::userEmail
    ]);

    #self::setGrantToken();
  }

  public function setGrantToken()
  {
    $path = $this->getPathToToken('zcrm_oauthtokens.txt');
    if(file_exists($path))
    {
      $oAuthTokens = unserialize(file_get_contents($path));
      print_r($oAuthTokens);
      $oAuthToken = $oAuthTokens[count($oAuthTokens)-1];
      echo $oAuthToken->getAccessToken();
      self::$grantToken = $oAuthToken->getAccessToken();
    }
  }

  public static function generateAccessTokenFromGrantToken(){

    $oAuthClient = ZohoOAuth::getClientInstance();
    $grantToken = self::$grantToken;
    $oAuthTokens = $oAuthClient->generateAccessToken($grantToken);
    var_dump($oAuthTokens);
    self::$refreshToken = $oAuthTokens->getRefreshToken();
    print_r($oAuthTokens->getRefreshToken());
  }
  public static function generateAccessTokenFromRefreshToken(){

    $oAuthClient = ZohoOAuth::getClientInstance();
    $refreshToken = self::$refreshToken;
    $userIdentifier = self::userEmail;
    $oAuthTokens = $oAuthClient->generateAccessTokenFromRefreshToken($refreshToken,$userIdentifier);
    print_r($oAuthTokens);
  }

  public static function generateAccessToken()
  {
    self::generateAccessTokenFromGrantToken();
    self::generateAccessTokenFromRefreshToken();
  }
}

echo (new AuthByToken())->generateAccessToken();
