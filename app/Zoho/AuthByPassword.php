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
            $password = "$4O|j 2)(0i.T/S"; //"\;'>?}9?=s=93Na";
            #$username = 'error';
        }
    }

    public function getToken($username = null, $password = null, $new = false, $scope = 'ZohoSupport/supportapi'): ?string
    {
        //$this->recheckUserName($username, $password);

        /*$token_path = $this->config->getPathToToken($username);
        if(file_exists($token_path) && !$new){
            $token = file_get_contents($token_path);
            if($token){
                return trim($token);
            }
        }else{
            Log::put(sprintf("file_put_contents %s", $token_path));
            file_put_contents($token_path, '');
            chmod($token_path, 0777);
        }*/

        $param = "SCOPE=".$scope."&EMAIL_ID=" . $username . "&PASSWORD=" . $password;
        $ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
        //echo $param."\n";
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $result = curl_exec($ch);
        //var_dump($result);exit;
        if(($result) === false){

            curl_close($ch);
            echo 'Ошибка curl: ' . curl_error($ch);
            throw new \Exception('Ошибка curl: ' . curl_error($ch), curl_errno());
        }else{
            //Log::put(sprintf("curl_exec %s", $result));
            #file_put_contents(__DIR__."/../log/zohov1.txt", $result." ___________".date("Y-m-d H:i:s"), FILE_APPEND);
            /*This part of the code below will separate the Authtoken from the result.
            Remove this part if you just need only the result*/
            $anArray = explode("\n", $result);
            $authToken = explode("=", $anArray['2']);
            $cmp = strcmp($authToken['0'], "AUTHTOKEN");
            #echo $anArray['2'] . "";
            if($cmp == 0){
                $token = trim($authToken['1']);
                #echo "Created Authtoken is : " . $authToken['1'];
                //file_put_contents($token_path, $token);
                curl_close($ch);
                return $token;
            }
        }
        return null;
    }

    public function getMyRecords($username = null)
    {
        try{
            $token = $this->getToken();
        }
        catch(\Exception $e)
        {
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => $e->getMessage()]
            );
        }
        $this->recheckUserName($username);
        $param = 'authtoken=' . $token . '&scope=crmapi&Email=' . $username . '&fromIndex=1&toIndex=2';
        echo $param;
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
}
