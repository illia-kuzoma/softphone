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
                'priority'      => $callData['priority'],
                'phone'         => $callData['phone'],
                'time_start'    => $callData['time_start'],
                'user_id'       => $callData['user_id']
            ],
            [
                'id'            => 'required|string|unique:report_missed_calls|max:128',
                'type'          => 'required|min:3',
                'first_name'    => 'required|max:20',
                'last_name'     => 'required|max:20',
                'business_name' => 'required|max:200',
                'contact'       => 'required|max:200',
                'priority'      => 'required|min:3',
                'phone'         => 'required|max:13',
                'time_start'    => 'date_format:Y-m-d H:i:s',
                'user_id'       => 'required|integer'
            ]
        );

        return ! $validator->fails();
    }

    /**
     * Insert Single Call Data to DB
     * @param $singleCallData
     */
    public function insertSingleCallData($singleCallData): void
    {
        if ( $this->validateBeforeInsert($singleCallData) ) {
            DB::table( 'report_missed_calls' )->insert(
                [
                    'id'            => $singleCallData['id'],
                    'type'          => $singleCallData['type'],
                    'first_name'    => $singleCallData['first_name'],
                    'last_name'     => $singleCallData['last_name'],
                    'business_name' => $singleCallData['business_name'],
                    'contact'       => $singleCallData['contact'],
                    'priority'      => $singleCallData['priority'],
                    'phone'         => $singleCallData['phone'],
                    'time_start'    => $singleCallData['time_start'],
                    'user_id'       => $singleCallData['user_id'],
                    'created_at'    => date( 'Y-m-d H:i:s' ),
                    'updated_at'    => date( 'Y-m-d H:i:s' ),
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
}
