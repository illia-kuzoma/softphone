<?php

namespace App\Zoho;
use zcrmsdk\oauth\ZohoOAuth;

/**
 * Data to configure zoho tokens locally.
 *
 * Docs https://www.zoho.com/projects/help/rest-api/get-tickets-api.html
 *
 * Link to get grant token https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL,Desk.tickets.READ,ZohoCRM.users.READ,ZohoCRM.org.READ,ZohoCRM.settings.READ,Desk.calls.READ,Desk.settings.READ,Desk.basic.READ,ZohoCRM.modules.accounts.READ,ZohoCRM.coql.READ,ZohoCRM.modules.leads.READ,ZohoSearch.securesearch.READ&client_id=1000.YZO05BI18M18TAUKJGUA38BKMVNYKH&response_type=code&access_type=offline&redirect_uri=https://www.wellnessliving.com&prompt=consent
 *
 * Link to get grant token https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL,Desk.tickets.READ,ZohoCRM.users.READ,ZohoCRM.org.READ,ZohoCRM.settings.READ,Desk.calls.READ,Desk.settings.READ,Desk.basic.READ,ZohoCRM.modules.accounts.READ,ZohoCRM.coql.READ,ZohoCRM.modules.leads.READ,ZohoSearch.securesearch.READ&client_id=1000.SP95RNDM8ATPVS67H15R5HMLNK5TMH&response_type=code&access_type=offline&redirect_uri=https://www.wellnessliving.com&prompt=consent
 *
 * Class AuthByToken
 * @package App\Zoho
 */
class AuthByToken extends Auth
{
    /**
     * @deprecated
     * @var string list of scopes to enter to zoho console to get grant Token. It field doesn't use in code. It is rudeement.
     */
    private static $scopes = 'ZohoCRM.modules.ALL,Desk.tickets.READ,ZohoCRM.users.READ,ZohoCRM.org.READ,ZohoCRM.settings.READ,Desk.calls.READ,Desk.settings.READ,Desk.basic.READ,ZohoCRM.modules.accounts.READ,ZohoCRM.coql.READ,ZohoCRM.modules.leads.READ,ZohoSearch.securesearch.READ';
    /**
     * @deprecated
     * @var string|null  Uses just only as field to contain a value.
     */
    private static $grantToken = '1000.e622781f464ad1124b7d3f2bd856eb80.388a25ea6c3fd6717714efa82ae01ad9';
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

