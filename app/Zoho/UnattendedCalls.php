<?php


namespace App\Zoho;


use zcrmsdk\crm\utility\ZCRMConfigUtil;

class UnattendedCalls
{

    public function __construct()
    {
        new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);

        #self::setGrantToken();
    }

    public function getAll()
    {
        $token =  ZCRMConfigUtil::getAccessToken();
        $ch = curl_init("https://desk.zoho.com/api/v1/sequentialUnattendedCalls/Count" . '?fromTime=2019-01-01T07:47:43.206Z&endTime=2020-02-23T07:47:43.206Z&from=1&limit=99');
        curl_setopt($ch, CURLOPT_HTTPHEADER,[
            'orgId:' . (new Organization())->getIdWellnessliving(),
            'Authorization:Zoho-oauthtoken ' . $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        echo $result ." !!\n\n";
        Log::put(sprintf("curl_exec %s", $result));
        curl_close($ch);
        return null;
    }
}
