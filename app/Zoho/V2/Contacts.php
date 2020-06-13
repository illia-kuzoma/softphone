<?php


namespace App\Zoho\V2;


use App\Zoho\Auth;
use App\Zoho\Config;
use zcrmsdk\crm\utility\ZCRMConfigUtil;

/**
 * https://www.zoho.com/crm/developer/docs/api/search-records.html
 *
 * Class Contacts
 * @package App\Zoho\V2
 */
class Contacts
{
    public function __construct()
    {
        new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);

        #self::setGrantToken();
    }

    /**
     * Поиск данные контакта по одному полю.
     *
     * @param $param string параметр п окоторому ищем.
     * @param $value string искомая строка.
     * @return array данные контакта если найдены.
     * @throws \ReflectionException
     * @throws \zcrmsdk\crm\exception\ZCRMException
     */
    private function searchInContacts($param, $value):array
    {
        $st = 'curl "https://www.zohoapis.com/crm/v2/'.(new \ReflectionClass($this))->getShortName().'/search?'.$param.'='.urlencode($value).'" -H "Authorization: Zoho-oauthtoken '.ZCRMConfigUtil::getAccessToken().'" -X GET';
        #print_r($st);exit;
        $string = exec($st);
        if($string)
        {
            $a_response = json_decode($string, true);
            return $this->parseResponse($a_response);
        }
        return [];
    }

    /**
     * Ищет данные контакта по Телефону.
     *
     * @param $value
     * @return array
     */
    public function searchInContactsByPhone($value):array
    {
        return $this->searchInContacts('phone', $value);
    }

    /**
     * Ищет данные контакта по email.
     *
     * @param $value
     * @return array
     */
    public function searchInContactsByEmail($value):array
    {
        return $this->searchInContacts('email',  $value);
    }
    public function searchInContactsByWord($value):array
    {
        return $this->searchInContacts('word', $value);
    }

    /**
     * Разбирает данные ответа Зохо с целью получить ссылку на сайт бизнесса Wellnessliving и его название.
     *
     * @param $a_response
     * @return array
     */
    private function parseResponse($a_response)
    {
        $a_return = [];
        if(isset($a_response['data'][0]))
        {
            $a_response_1st = $a_response['data'][0];
            $a_return['name'] = $a_response_1st['Account_Name']['name']??null;
            $a_return['link'] = $a_response_1st['Business_Link']??null;
            /*$a_return[] = $a_response_1st['Business_ID']??null;
            $a_return[] = $a_response_1st['Account_Name']['id']??null;
            $a_return[] = $a_response_1st['Legal_Business_Name']??null;
            $a_return[] = $a_response_1st['Industry']??null;
            $a_return[] = $a_response_1st['Business_Link']??null;
            $a_return[] = $a_response_1st['Business_Group']??null;*/
        }
        return $a_return;
    }
}
