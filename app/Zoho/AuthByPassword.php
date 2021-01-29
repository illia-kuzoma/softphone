<?php

namespace App\Zoho;

class AuthByPassword extends Auth
{
    public $token_file_name = "_token_by_pass.txt";

    /**
     * Zoho response.
     *
     * @var array
     */
    private $cause = [];

    private $config;

    public function __construct()
    {
        $this->config = new Config([
            "redirect_uri" => self::redirect_uri,
            "currentUserEmail" => self::userEmail
        ]);

        #self::setGrantToken();
    }

    /**
     * Ответ Зохо о причине не получения токена.
     *
     * @return array
     */
    public function getCause(): string
    {
        return $this->cause['CAUSE'];
    }

    /**
     * Записать ответ Зохо о причине не получения токена.
     *
     * @param array $cause
     */
    public function setCause(array $cause): void
    {
        $this->cause = $cause;
    }

    public function getToken($username = null, $password = null, $new = false, $scope = 'ZohoCRM/crmapi'): ?string
    {
        $param = "SCOPE=".$scope."&EMAIL_ID=" . $username . "&PASSWORD=" . urlencode($password);
        $ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $result = curl_exec($ch);
        if(($result) === false){

            curl_close($ch);
            echo 'Ошибка curl: ' . curl_error($ch);
            throw new \Exception('Ошибка curl: ' . curl_error($ch), curl_errno());
        }else{
            $anArray = explode("\n", $result);
            $authToken = explode("=", $anArray['2']);
            if(!empty($authToken['0']))
            {
                $message = trim($authToken['1']);
                switch($authToken['0'])
                {
                    case "AUTHTOKEN":
                        {
                            $s_token = $message;
                        }break;
                    case "CAUSE":
                        {
                            $s_token = '';
                            $this->setCause(['CAUSE'=>$authToken['1']]);
                            if(!$s_token){ // Токен не был олучен от Зохо.
                                // анализирую причину.
                                switch($this->getCause()){
                                    case'EXCEEDED_MAXIMUM_ALLOWED_AUTHTOKENS':
                                        // в этом случае токен в порядке, а значит переданный от клиента пароль и имейл есть в зохо.
                                        // А значит можно смело обновлять пароль в БД, даже если переданный от клиента не совпадает с тем что в БД.
                                        break;
                                    case'DEPRECATED_AUTHTOKEN_SCOPE':
                                        // https://help.zoho.com/portal/en/community/topic/deprecating-support-for-authtokens
                                        // Изза того что Зохо закрыло этот канал получения данных о пользовательском токене то этот ответ я воспринимаю как
                                        // с данными пользователя все хооршо, только зохо просит пользоватся тоеном приложения.

                                        // в этом случае токен в порядке, а значит переданный от клиента пароль и имейл есть в зохо.
                                        // А значит можно смело обновлять пароль в БД, даже если переданный от клиента не совпадает с тем что в БД.
                                        break;
                                    case'INVALID_PASSWORD':
                                        // Ошибка в пароле.
                                    default: // вывожу сообщение от Зохо
                                        throw new \Exception($this->getCause());
                                }
                            }
                        }break;
                }
            }
            curl_close($ch);
            return $s_token;
        }
        return null;
    }
}
