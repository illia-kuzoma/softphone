<?php

namespace App\Models;

use App\Zoho\V2\Contacts;
use Validator;

/**
 * Class ReportUnattendedCall
 * @package App\Models
 */
class ReportUnattendedCall extends ReportUnattended
{
    use Common;

    const PAGES_PER_PAGE = 20;
    const TABLE_NAME = "report_unattended_call";

    public $table = "";

    /**
     * Выборка пропущенных звонков списоком. С возможноснями сортировки, выборок и поиска.
     *
     * @param string $dateStart
     * @param string $period
     * @param string $uid
     * @param string $searchWord
     * @param string $sortField
     * @param string $sortBy
     * @param int $page
     * @return array
     */
    public function getList($dateStart = '', $period = '',
                            $searchWord = '', $sortField = 'time_start', $sortBy = 'desc',
                            $page = 1): array
    {
        $searchWord = $this->getSearchWord($searchWord);
        $sortField = $this->getSortField($sortField);
        $sortBy = $this->getSortOrder($sortBy);
        $page = $this->getPage($page);

        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';

        $call_list_q = ReportUnattendedCall::query()->select([
            //'users.id as user_id',
            $this->table.'.id',
            $this->table.'.business_name',
            $this->table.'.contact',
            $this->table.'.agent_id',
            $this->table.'.priority',
            $this->table.'.phone',
            $this->table.'.time_start',
            'users.first_name',
            'users.last_name'
        ])->join('users', $this->table.'.agent_id', '=', 'users.id');

        $a_filter_by_agents = $this->getAgentIdFilter();
       #print_r($a_filter_by_agents);exit;
        if(!empty($a_filter_by_agents))
            $call_list_q->whereIn($this->table.'.agent_id', $a_filter_by_agents);

        $call_list_q->where('time_start', '>=', $dateFrom);
        $call_list_q->where('time_start', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );
        /*if($uid)
        {
            $call_list_q->when(request($this->_getIdKey(), $uid), function ($q, $uid) {
                return $q->where($this->_getIdKey(), $uid);
            });
        }*/
        if($searchWord)
        {
            $call_list_q->where(function ($q) use ($searchWord)
            {
                return $q->where('contact', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('business_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('phone', 'LIKE', '%'.$searchWord.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$searchWord.'%');
            });
        }
        $calls_cnt = $call_list_q->count();
        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);
        if($page > $pages_count){
            $page = $pages_count;
        }
        $call_list_q->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE);
        $call_list = $call_list_q->get();
        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    private function getContactName($s_contact): string
    {
        $a_contact = json_decode($s_contact, true);
        $a_contact = array_diff($a_contact, array(''));
        $a_contact['name'] = implode(' ', array_unique(explode(' ', $a_contact['name'])));
        return implode(', ', $a_contact);
    }

    private $a_business_data = [];
    public function getBusinessData($call): array
    {
        $a_business_data = [];
        if(empty($call->business_name))
        {
            // Если данных в БД нет
            $a_business = $this->parseBusinessData($call);
        }
        else
        {
            // Если данные по бизнесу в БД есть.
            $a_business = json_decode($call->business_name, true);
        }
        $a_business_data['name'] = $a_business['name'];
        $a_business_data['link'] = $a_business['link'];
        return $a_business_data;
    }

    /**
     * Format Call List
     * @param $data
     * @param $page
     *
     * @return array
     */
    private function formatDataCallList($data): array
    {
        $result = [];
        if ( ! empty( $data ) )
        {
            foreach ( $data as $item )
            {
               // $business_data = $this->getBusinessData($item);
                $business_data = json_decode($item->business_name, true);
                $result[] = [
                    'uid'         => $this->_getIdVal($item),//$item->user_id,
                    'business'    => [
                        'business_name' => $business_data['name'],
                        'business_link' => $business_data['link'],
                    ],
                    'contact'     => [
                        'contact_name' => $this->getContactName($item->contact),
                        'contact_link' => "https://desk.zoho.com/support/wellnessliving/ShowHomePage.do#Calls/dv/". $item->id,
                    ],
                    'user_data'   => ( new User() )->getUserData( $item->agent_id ), // TODO need to optimization in One request with all agent_ids.
                    'priority'    => $item->priority,
                    'phone'       => $item->phone,
                    'time_create' => strtotime( $item->time_start ),
                ];
            }
        }

        $this->updateBusinessName();

        return $result;
    }

    public function updateBusinessName()
    {
        if($this->a_business_data)
        {
            foreach($this->a_business_data as $id=>$s_business)
            {
                \DB::table( $this->table )->where('id','=', $id)->update(
                    [
                        'business_name' => $s_business,
                    ]
                );
            }
            $this->a_business_data = [];
        }
    }

    /**
     * Insert new Call
     * @param $callData
     */
    public function insert($callData): void
    {
        if(empty($callData)){
            return;
        }
        if ( $this->isMultipleArray($callData) ) {
            $this->insertMultipleCallData($callData);
        } else {
            $this->insertSingleCallData($callData);
        }
    }

    /**
     * Validate Before Insert
     * @param $callData
     *
     * @return bool
     */
    public function validateBeforeInsert( $callData ): bool
    {
        $validator = Validator::make(
            [
                'id'            => $callData['id'],
                'business_name' => $callData['business_name'],
                'contact'       => $callData['contact'],
                'priority'      => $callData['priority']?$callData['priority']:'low',
                'phone'         => $callData['phone'],
                'time_start'    => $callData['time_start'],
                'agent_id'      => $callData['agent_id'],
                'user_id'       => $callData['user_id']
            ],
            [
                'id'            => 'required|digits_between:10,19', // required|string|unique:report_missed_calls|
                'business_name' => 'max:200',
                'contact'       => 'required|max:200',
                'priority'      => 'required|min:3',
                'phone'         => 'max:20',
                'time_start'    => 'date_format:Y-m-d H:i:s',
                'agent_id'       => 'required|digits_between:10,19',
                'user_id'       => 'required|digits_between:10,19'
            ]
        );
        if($validator->fails()){
            print_r($validator->errors());
            echo $callData['phone'];
        }
        return ! $validator->fails();
    }

    /**
     * Insert Single Call Data to DB
     * @param $singleCallData
     */
    public function insertSingleCallData($singleCallData): void
    {
        $singleCallData['time_start'] = date(self::DATE_TIME_FORMAT, strtotime($singleCallData['time_start']));
        if ( $this->validateBeforeInsert($singleCallData) ) {
            $res = \DB::table( $this->table )->updateOrInsert(['id' => $singleCallData['id']],
                [
                    'id'            => $singleCallData['id'],
                    'business_name' => $singleCallData['business_name'],
                    'contact'       => $singleCallData['contact'],
                    'priority'      => $singleCallData['priority']??self::PRIORITY_LOW,
                    'phone'         => $singleCallData['phone'],
                    'time_start'    => $singleCallData['time_start'],
                    'user_id'       => $singleCallData['user_id'],
                    'agent_id'       => $singleCallData['agent_id'],
                    'created_at'    => date( self::DATE_TIME_FORMAT ),
                    'updated_at'    => date( self::DATE_TIME_FORMAT ),
                ]
            );
        }
    }

    /**
     * Insert Multiple Call Data to DB
     * @param $multipleCallData
     */
    public function insertMultipleCallData($multipleCallData): void
    {
        foreach ($multipleCallData as $singleCallData) {
            $this->insertSingleCallData($singleCallData);
        }
    }

    public function updateDB()
    {
        $this->updateEmptyBusinessNames();
    }

    public function updateEmptyBusinessNames()
    {
        $call_list_q = ReportUnattendedCall::query()->select([
            //'users.id as user_id',
            $this->table.'.id',
            $this->table.'.contact',
            $this->table.'.phone'
        ])->where('business_name', '=','');
        $call_list = $call_list_q->get();
print_R(count($call_list));exit;
        if ( ! empty( $call_list ) )
        {
            foreach ( $call_list as $item )
            {
                $this->parseBusinessData($item);
            }
        }
        $this->updateBusinessName();
    }

    private function getBusinessDataByContacts($a_contact)
    {
        $o_zoho_contacts = new Contacts();
        $a_contacts = [];
        #print_r($a_contact);exit;
        foreach($a_contact as $field=>$value){

            if(!$value)
                continue;

            switch($field)
            {
                case 'email':
                    $a_contacts = $o_zoho_contacts->searchInContactsByEmail($value);
                    break;
                case 'phone':
                    $a_contacts = $o_zoho_contacts->searchInContactsByPhone($value);
                    break;
                /*case 'name':
                    $o_zoho_contacts->searchInContactsByEmail($value);
                    break;*/
            }
            if($a_contacts)
            {
                break;
            }
        }
        return $a_contacts;
    }

    /**
     * Getting business data from remote server.
     * @param $call
     * @return array
     */
    private function parseBusinessData($call)
    {
        $a_contact = json_decode($call->contact, true);
        if(empty($a_contact['phone']))
        {
            $a_contact['phone'] = $call->phone;
        }
        $a_business = $this->getBusinessDataByContacts($a_contact);
        if(empty($a_business['name']))
        {
            $a_business['name'] = $a_contact['name'];
        }
        if(empty($a_business['link']))
        {
            $a_business['link'] = null;
        }
        $this->a_business_data[$call->id] = json_encode($a_business);
        return $a_business;
    }

}
