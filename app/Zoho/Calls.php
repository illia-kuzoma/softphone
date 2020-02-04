<?php
namespace App\Zoho;

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class Calls
{
  public function __construct()
  {
    new Config([
      "redirect_uri"=>Auth::redirect_uri,
      "currentUserEmail"=>Auth::userEmail
    ]);

    #self::setGrantToken();
  }

    public function getActivities($time_start, $is_since = true, $page = 1, $per_page = 200)
    {
        $activities_arr = [];
        #$time_since = "2019-09-15T15:26:49+05:30";
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance("Activities"); // To get module instance
        /* For VERSION <=2.0.6 $response = $moduleIns->getRecords(null, null, null, 1, 100, null); // to get the records(parameter - custom_view_id,field_api_name,sort_order,customHeaders is optional and can be given null if not required), customheader is a keyvalue pair for eg("if-modified-since"=>"2008-09-15T15:53:00")*/
        $param_map=array("page"=>$page,"per_page"=>$per_page, 'sort_order'=>'desc', 'sort_by'=>'Created_Time'); // key-value pair containing all the parameters - optional
        $header_map = [];
        if($is_since)
        {
            $header_map = array("if-modified-since"=>$time_start); // key-value pair containing all the headers - optional
        }
       /* print_r($param_map);
        print_r($header_map);
        echo $time_start;exit;
        exit;*/
        $i=0;
        $start_time = time();
        do
            {
            try
            {
                $response = $moduleIns->getRecords($param_map,$header_map); // to get the records($param_map - parameter map,$header_map - header map
            }
            catch(\Exception $e){
                #echo $e->getMessage()."  call break";exit;
                break;
            }
            $param_map['page']++;
            $records = $response->getData(); // To get response data

            if(!$records)
            {
                break;
            }

            try {
                foreach ($records as $record)
                {
                    $a_result = [];
                    $a_result['id'] = $record->getEntityId();

                    $createdBy = $record->getCreatedBy();
                    $a_result['created_by']['id'] = $createdBy->getId();
                    $a_result['created_by']['name'] = $createdBy->getName();

                    $modifiedBy = $record->getModifiedBy();
                    $a_result['modified_by']['id'] = $modifiedBy->getId();
                    $a_result['modified_by']['name'] = $modifiedBy->getName();

                    $owner = $record->getOwner();
                    $a_result['owner']['id'] = $owner->getId();
                    $a_result['owner']['name'] = $owner->getName();
                    $a_result['created_time'] = $owner->getCreatedTime();

                    $map = $record->getData(); // To get record data as map
                    foreach ($map as $key => $value) {
                        if ($value instanceof ZCRMRecord) // If value is ZCRMRecord object
                        {
                            $module_id = $value->getModuleApiName();
                            $a_result['ZCRMRecord'][$module_id]['entity_id'] = $value->getEntityId();
                            $a_result['ZCRMRecord'][$module_id]['module_api_name'] = $value->getModuleApiName();
                            $a_result['ZCRMRecord'][$module_id]['lookup_label'] = $value->getLookupLabel();
                        }
                        else // If value is not ZCRMRecord object
                        {
                            if($key == 'Activity_Type')
                            {
                                $a_result['activity_type'] = $value;
                            }
                            if($key == 'Priority')
                            {
                                $a_result['priority'] = $value;
                            }
                            if($key == 'Call_Start_Time')
                            {
                                if(is_string($value) && is_int(strtotime($value)) && strtotime($value) > 0)
                                {
                                    if(strtotime($value) < strtotime($time_start))
                                    {
                                        // Выход если пошли записи со временем старта звонка меньше времени выборки.
                                        break 3;
                                    }
                                }
                                $a_result['call_start_time'] = $value;
                            }
                            if($key == 'Call_Duration')
                            {
                                $a_result['call_duration'] = $value;
                            }
                            if($key == 'Subject')
                            {
                                $a_result['subject'] = $value;
                            }
                            if($key == 'Call_Type')
                            {
                                $a_result['call_type'] = $value;
                            }
                            if($key == 'Call_Status')
                            {
                                $a_result['call_status'] = $value;
                            }
                            if($key == 'Call_Duration_in_seconds')
                            {
                                $a_result['duration_in_seconds'] = $value;
                            }
                        }
                    }
                    if($this->_checkMissedCall($a_result))
                    {
                        $activities_arr[] = $this->prepareActivityData($a_result);
                    }
                }
            } catch (ZCRMException $ex) {
                echo $ex->getMessage(); // To get ZCRMException error message
                echo $ex->getExceptionCode(); // To get ZCRMException error code
                echo $ex->getFile(); // To get the file name that throws the Exception
            }

        }
        while((time() - $start_time)<1800);
        return $activities_arr;
    }

    private function _checkMissedCall($data)
    {
        if((!$data['duration_in_seconds'] || $data['duration_in_seconds'] == 0) &&
            $data['activity_type'] == 'Calls')
        {
            return true;
        }
        return false;
    }

    public function prepareActivityData($data)
    {
        $phone = '';
        if($data['subject']){
            $phone = preg_replace("/[^0-9]/", '', $data['subject']);
        }
        [$fname,$lname] = explode(' ',$data['owner']['name']);
        $a_activity = [
            'id'=>$data['id'],
            'type'=>($data['activity_type'] == 'Calls')?'call':'-|-',
            'first_name'=>$fname,
            'last_name'=>$lname,
            'phone'=>strlen($phone)>7?$phone:'',
            'contact'=>$data['ZCRMRecord']['Who_Id']['lookup_label']??$data['subject'],
            'priority'=>$data['priority'],
            'business_name'=>$data['ZCRMRecord']['What_Id']['lookup_label']??'',
            'time_start'=>$data['call_start_time'],
            'user_id'=>$data['owner']['id'],
        ];
        return $a_activity;
    }
}
