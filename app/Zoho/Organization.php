<?php
namespace App\Zoho;

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\utility\ZCRMConfigUtil;

class Organization
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
        $a_result = [];
        $token =  ZCRMConfigUtil::getAccessToken();
        $ch = curl_init("https://desk.zoho.com/api/v1/organizations");
        curl_setopt($ch, CURLOPT_HTTPHEADER,[
            'Authorization:Zoho-oauthtoken ' . $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(($s_result = curl_exec($ch)) === false)
        {
            echo 'Ошибка curl: ' . curl_error($ch);
        }else{
            Log::put(sprintf("curl_exec %s", $s_result));
            $a_result = json_decode($s_result, true);
        }
        curl_close($ch);
        return $a_result;
    }

    public function getOrgId()
    {
        $a_org = $this->getAll()['data'];
        $i_wellnessliving_key = -1;
        foreach ($a_org as $key => $org) {
            foreach ($org as $data) {
                if(mb_strtolower($data) === 'wellnessliving'){
                    $i_wellnessliving_key = $key;
                    break 2;
                }
            }
        }
        if($i_wellnessliving_key >= 0)
        {
            return $a_org[$i_wellnessliving_key]['id'];
        }
        return null;
    }
}
