<?php

namespace App\Zoho;
use zcrmsdk\oauth\ZohoOAuth;

class AuthByToken extends Auth
{
    /**
     * @var string list of scopes to enter to zoho console to get grant Token. It field doesn't use in code.
     */
    private static $scopes = 'ZohoCRM.modules.ALL,ZohoCRM.users.ALL,ZohoCRM.org.ALL,ZohoCRM.settings.ALL,Desk.calls.READ,Desk.settings.READ,Desk.basic.READ';
    private static $grantToken = '1000.3a4b523d7d4c150d4516f193d72e2cc1.25d6b295a69ab887944b9dbfb1985545';
    private static $refreshToken = '';
    public $token_file_name = '';
    /**
     * @var Config
     */
    private $config;

    public function __construct($token = null)
    {
        $this->config = new Config([
            "redirect_uri"=>self::redirect_uri,
            "currentUserEmail"=>self::userEmail
        ]);
        if($token)
        {
            self::$grantToken = $token;
        }
        #self::setGrantToken();
    }

    public function setGrantToken()
    {
        $path = $this->config->getPathToToken('zcrm_oauthtokens.txt');
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
        echo "\nok\n";
    }
}

