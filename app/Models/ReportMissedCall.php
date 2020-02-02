<?php

namespace App\Models;

use DB;
use Validator;

class ReportMissedCall extends ReportMissed
{
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

        $call_list_q = ReportMissedCall::query();
        $call_list_q->where('time_start', '>=', $dateFrom);
        $call_list_q->where('time_start', '<=', $dateTo);
        $call_list_q->orderBy( $sortField, $sortBy );
        if($uid)
        {
            $call_list_q->when(request('user_id', $uid), function ($q, $uid) {
                return $q->where('user_id', $uid);
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
                    'uid'         => $item->user_id,
                    'business'    => [
                        'business_name' => $item->business_name,
                        'business_link' => "https://zoho.url.com", // new field in DB ???
                    ],
                    'contact'     => [
                        'contact_name' => $item->contact,
                        'contact_link' => "https://ufo.url.com",
                    ],
                    'user_data'   => ( new User() )->getUserData( $item->user_id ),
                    'priority'    => $item->priority,
                    'phone'       => $item->phone,
                    'time_create' => strtotime( $item->created_at ),
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
        return (new ReportMissedGraph())->getList($dateStart, $period);
    }

    /**
     * Check array Single or Multiple
     * @param $data
     *
     * @return bool
     */
    public function isMultipleArray( $data ): bool
    {
        return count( array_keys( $data ) ) > 1;
    }

    /**
     * Insert new Call
     * @param $callData
     */
    public function insert($callData): void
    {
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
                'type'          => $callData['type'],
                'first_name'    => $callData['first_name'],
                'last_name'     => $callData['last_name'],
                'business_name' => $callData['business_name'],
                'contact'       => $callData['contact'],
                'priority'      => $callData['priority']?$callData['priority']:'low',
                'phone'         => $callData['phone'],
                'time_start'    => $callData['time_start'],
                'user_id'       => $callData['user_id']
            ],
            [
                'id'            => 'max:128', // required|string|unique:report_missed_calls|
                'type'          => 'required|min:3',
                'first_name'    => 'required|max:20',
                'last_name'     => 'required|max:20',
                'business_name' => 'max:200',
                'contact'       => 'required|max:200',
                'priority'      => 'required|min:3',
                'phone'         => 'max:20',
                'time_start'    => 'date_format:Y-m-d H:i:s',
                'user_id'       => 'required|integer'
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
        $singleCallData['phone'] = $singleCallData['phone']?substr($singleCallData['phone'],0,19):'---';
        $singleCallData['business_name'] = $singleCallData['business_name']??'-';
        $singleCallData['time_start'] = date(self::DATE_TIME_FORMAT, strtotime($singleCallData['time_start']));
        if ( $this->validateBeforeInsert($singleCallData) ) {
            $res = \DB::table( 'report_missed_calls' )->updateOrInsert(['id'            => $singleCallData['id']],
                [
                    'id'            => $singleCallData['id'],
                    'type'          => strtolower($singleCallData['type']),
                    'first_name'    => $singleCallData['first_name'],
                    'last_name'     => $singleCallData['last_name'],
                    'business_name' => $singleCallData['business_name'],
                    'contact'       => $singleCallData['contact'],
                    'priority'      => $singleCallData['priority']??self::PRIORITY_LOW,
                    'phone'         => $singleCallData['phone'],
                    'time_start'    => $singleCallData['time_start'],
                    'user_id'       => $singleCallData['user_id'],
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
        return DB::table('report_missed_calls')->max('time_start');
    }
    public function maxRecordTimeCreate()
    {
        return DB::table('report_missed_calls')->max('created_at');
    }

    public function updateDB($a_records = [], $time_to_select = null)
    {
        if($a_records)
        {
            $this->insert($a_records);
            $grouped_data_by_days = \DB::select(
                'select report_missed_calls.first_name, report_missed_calls.last_name, 
report_missed_calls.user_id, count(report_missed_calls.id) as count,  
DATE_FORMAT((report_missed_calls.time_start),"%Y-%m-%d") as day from report_missed_calls 
'.($time_to_select?' WHERE report_missed_calls.time_start >= "'.date(self::DATE_TIME_FORMAT,strtotime($time_to_select)).'" ':'').'
GROUP BY report_missed_calls.first_name, report_missed_calls.last_name, 
 DATE_FORMAT(report_missed_calls.time_start,"%Y %M %d")',
                [],
                true
            );
            if(!empty($grouped_data_by_days))
            {
                $missedCalls = new ReportMissedGraph();
                $missedCalls->insert($grouped_data_by_days);

                $missedCalls = new User();
                $missedCalls->insert($grouped_data_by_days);
            }
        }
    }
}
