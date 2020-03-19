<?php

namespace App\Models;

use DB;
use Validator;

/**
 * Class ReportUnattendedCall
 * @package App\Models
 */
class ReportUnattendedCall extends ReportUnattended
{

    use Common;

    public $table = "report_unattended_call";
    const PAGES_PER_PAGE = 20;

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
    public function getList($dateStart = '', $period = '', $uid = '',
                            $searchWord = '', $sortField = 'time_start', $sortBy = 'desc',
                            $page = 1): array
    {
        $uid = $this->getUid($uid);
        $searchWord = $this->getSearchWord($searchWord);
        $sortField = $this->getSortField($sortField);
        $sortBy = $this->getSortOrder($sortBy);
        $page = $this->getPage($page);

        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';

        $call_list_q = ReportUnattendedCall::query()->join('users', $this->table.'.agent_id', '=', 'users.id');
        $call_list_q->where('time_start', '>=', $dateFrom);
        $call_list_q->where('time_start', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );
        if($uid)
        {
            $call_list_q->when(request($this->_getIdKey(), $uid), function ($q, $uid) {
                return $q->where($this->_getIdKey(), $uid);
            });
        }
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
        $call_list_q->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE);
        $call_list = $call_list_q->get();

        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);

        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    private function getContactName($s_contact)
    {
        $a_contact = json_decode($s_contact, true);
        $a_contact = array_diff($a_contact, array(''));
        $a_contact['name'] = implode(' ', array_unique(explode(' ', $a_contact['name'])));
        return implode(', ', $a_contact);
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
        if ( ! empty( $data ) ) {
            foreach ( $data as $item ) {

                $result[] = [
                    'uid'         => $this->_getIdVal($item),//$item->user_id,
                    'business'    => [
                        'business_name' => $item->business_name,
                        'business_link' => "https://zoho.url.com", // new field in DB ???
                    ],
                    'contact'     => [
                        'contact_name' => $this->getContactName($item->contact),
                        'contact_link' => "https://ufo.url.com",
                    ],
                    'user_data'   => ( new User() )->getUserData( $item->agent_id ),
                    'priority'    => $item->priority,
                    'phone'       => $item->phone,
                    'time_create' => strtotime( $item->time_start ),
                ];
            }
        }

        return $result;
    }

    /**
     * get Diagram list
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getDiagramList($dateStart = null, $period = null): array
    {
        return (new ReportUnattendedGraph())->getList($dateStart, $period);
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
        #print_r($singleCallData);exit;
        $singleCallData['business_name'] = $singleCallData['business_name']??'-';
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

    public function maxTimeCreate()
    {
        return DB::table($this->table)->max('time_start');
    }
    public function maxRecordTimeCreate()
    {
        return DB::table($this->table)->max('created_at');
    }

    public function updateDB()
    {

    }
}
