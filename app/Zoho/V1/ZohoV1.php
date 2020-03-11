<?php
namespace App\Zoho\V1;

use zcrmsdk\crm\utility\ZCRMConfigUtil;

class ZohoV1
{
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
