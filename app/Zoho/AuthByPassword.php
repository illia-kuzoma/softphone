<?php

namespace App\Zoho;

class AuthByPassword extends Auth
{
    public $token_file_name = "_token_by_pass.txt";

    private $config;

    public function __construct()
    {
        $this->config = new Config([
            "redirect_uri" => self::redirect_uri,
            "currentUserEmail" => self::userEmail
        ]);

        #self::setGrantToken();
    }

    public function recheckUserName(&$username = null, &$password = null)
    {
        if(!$username){
            $username = self::userEmail;
            $password = "\;'>?}9?=s=93Na";
            $username = 'error';
        }
    }

    public function getToken($username = null, $password = null, $new = false): ?string
    {
        $this->recheckUserName($username, $password);

        $token_path = $this->config->getPathToToken($username);
        if(file_exists($token_path) && !$new){
            $token = file_get_contents($token_path);
            if($token){
                return $token;
            }
        }else{
            Log::put(sprintf("file_put_contents %s", $token_path));
            file_put_contents($token_path, '');
            chmod($token_path, 0777);
        }

        $param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=" . $username . "&PASSWORD=" . $password;
        $ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

        if(($result = curl_exec($ch)) === false){

            echo 'Ошибка curl: ' . curl_error($ch);
            throw new \Exception('Ошибка curl: ' . curl_error($ch), curl_errno());
        }else{
            Log::put(sprintf("curl_exec %s", $result));
            #file_put_contents(__DIR__."/../log/zohov1.txt", $result." ___________".date("Y-m-d H:i:s"), FILE_APPEND);
            /*This part of the code below will separate the Authtoken from the result.
            Remove this part if you just need only the result*/
            $anArray = explode("\n", $result);
            $authToken = explode("=", $anArray['2']);
            $cmp = strcmp($authToken['0'], "AUTHTOKEN");
            #echo $anArray['2'] . "";
            if($cmp == 0){
                #echo "Created Authtoken is : " . $authToken['1'];
                file_put_contents($token_path, $authToken['1']);
                return $authToken['1'];
            }
        }
        curl_close($ch);
        return null;
    }

    public function getMyRecords($username = null)
    {
        $this->recheckUserName($username);
        $param = 'authtoken=' . $this->getToken() . '&scope=crmapi&Email=' . $username . '&fromIndex=1&toIndex=2';
        $ch = curl_init("https://crm.zoho.com/crm/private/xml/Leads/getMyRecords");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $result = curl_exec($ch);
        echo $result;
        Log::put(sprintf("curl_exec %s", $result));
        curl_close($ch);
        return null;
    }


    public function sequentialUnattendedCallsCount($username = null)
    {
        $token = '1000.3d0a155402dbb59f776fd63adb1e67c0.a41ea557a6a8d7e402690098b2056f60s';
        $this->recheckUserName($username);
        #$param = 'authtoken=' . $this->getToken() . '&scope=crmapi&Email=' . $username . '&fromIndex=1&toIndex=2';
        #$param = ;
        $ch = curl_init("https://desk.zoho.com/api/v1/sequentialUnattendedCalls/Count" . '?fromTime=2019-01-01T07:47:43.206Z&endTime=2020-01-23T07:47:43.206Z&from=1&limit=99');
        curl_setopt($ch, CURLOPT_HTTPHEADER,[
           // 'orgId:2389290',
            'Authorization:Zoho-oauthtoken ' . $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        echo $result;
        Log::put(sprintf("curl_exec %s", $result));
        curl_close($ch);
        return null;
    }

}
