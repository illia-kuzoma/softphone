<?php

namespace App\Console\Commands;

use App\Models\ReportMissedCall;
use App\Zoho\Auth;
use App\Zoho\AuthByPassword;
use App\Zoho\AuthByToken;
use App\Zoho\Config;
use App\Zoho\Organization;
use App\Zoho\UnattendedCalls;
use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\utility\ZCRMConfigUtil;
use zcrmsdk\oauth\ZohoOAuth;
use zcrmsdk\oauth\ZohoOAuthClient;

class ZohoV1Requests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:v1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zo = new UnattendedCalls();
        $res = $zo->getAll();
        print_r($res);
        exit;
        /*$zo = new Organization();
        $res = $zo->getIdWellnessliving();
        print_r($res);
        exit;*/
         /*$zo = new AuthByToken();
         $zo->generateAccessToken();
         exit;*/
        /*$zo = new AuthByPassword();
        $res = $zo->organizations();
        var_dump($res);
        exit;*/
        $zo = new AuthByPassword();
        $res = $zo->sequentialUnattendedCallsCount();
        var_dump($res);
        exit;
echo ZCRMConfigUtil::getAccessToken();exit;
       # curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.ZCRMConfigUtil::getAccessToken()));



        $config = new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);
        $path = $config->getPathToToken('zcrm_oauthtokens.txt');
        if(file_exists($path))
        {
            $oAuthTokens = unserialize(file_get_contents($path));
            print_r($oAuthTokens);
            $oAuthToken = $oAuthTokens[count($oAuthTokens)-1];
            try {
                $oAuthToken->getAccessToken();
            }
            catch (\Exception $e){

            }
        }
exit;

        $inst = ZCRMRestClient::initialize(new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]));

        echo $inst->getAccessToken();
        exit;
        $persistence =  ZohoOAuthClient::getInstance(new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]));
        var_dump($persistence);exit;
        $missedCalls = new ReportMissedCall();
        $res = $missedCalls->loadFromRemoteServer();
        /*$zo = new AuthByPassword();
        $res = $zo->sequentialUnattendedCallsCount();*/
        var_dump($res);
    }
}
