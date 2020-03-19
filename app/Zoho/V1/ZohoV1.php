<?php
namespace App\Zoho\V1;

use App\Zoho\Auth;
use App\Zoho\Config;
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
            throw new \Exception(sprintf("Response in %s has error!\n %s", __METHOD__, json_encode($data)));
        }
    }

    protected function request($s_url, $s_param = '', $a_headers = [])
    {
        $a_result = [];
        $ch = curl_init($s_url . '?' . $s_param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            ['Authorization:Zoho-oauthtoken ' . ZCRMConfigUtil::getAccessToken()],
            $a_headers
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(($s_result = curl_exec($ch)) === false)
        {
            Log::put(sprintf("Error curl: %s", curl_error($ch)));
        }
        else
        {
            // Log::put(sprintf("curl_exec %s", $s_result));
            $a_result = json_decode($s_result, true);
        }
        curl_close($ch);
        return $a_result;
    }
}