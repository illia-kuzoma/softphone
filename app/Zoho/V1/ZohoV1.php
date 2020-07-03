<?php
namespace App\Zoho\V1;

use App\Zoho\Auth;
use App\Zoho\Config;
use App\Zoho\Log;
use zcrmsdk\crm\utility\ZCRMConfigUtil;

class ZohoV1
{
    public function __construct()
    {
        new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);
    }

    protected function checkResponse($data): void
    {
        if (!isset($data['data']))
        {
            throw new \Exception(sprintf("Response structure in %s has error!\n %s", __METHOD__, json_encode($data)));
        }
    }

    protected function request($s_url, $s_param = '', $a_headers = [])
    {
        $a_result = [];
        $url = $s_url . (($s_param)?'?' . $s_param:'');
        $ch = curl_init($url);
        //echo $url."\n\n";
        $a_headers[] = 'Authorization:Zoho-oauthtoken ' . ZCRMConfigUtil::getAccessToken();
        //print_r($a_headers);
        //echo "\n\n";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            $a_headers
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(($s_result = curl_exec($ch)) === false)
        {
            Log::put(sprintf("Error curl: %s", curl_error($ch)));
            throw new \Exception(sprintf("Response in %s has error!\n %s\n code err is %d\n",
                __METHOD__, curl_error($ch), curl_error($ch)));
        }
        else
        {
            print_r($s_result);
            // Log::put(sprintf("curl_exec %s", $s_result));
            $a_result = json_decode($s_result, true);
        }
        curl_close($ch);
        return $a_result;
    }
}
