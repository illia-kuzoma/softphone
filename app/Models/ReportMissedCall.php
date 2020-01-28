<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMissedCall extends Model
{
    const PAGES_PER_PAGE = 20;

    /**
     * @param null $dateStart
     * @param null $period
     * @param null $uid
     * @param null $searchWord
     * @param null $sortField
     * @param string $sortBy
     * @param int $page
     *
     * @return array
     */
    public function getList($dateStart = null, $period = null, $uid = null,
                            $searchWord = null, $sortField = null, $sortBy = 'DESC',
                            $page = 1): array
    {

        // set dateStart
        $dateStart = $dateStart ?? '';
        $dateStart = ! empty( $dateStart ) ? date( 'Y-m-d', strtotime( $dateStart ) ) : '';
        $search_condition = '=';

        // set period
        $period = $period ?? '';
        $period = ! empty( $period ) ? strtolower( (string) $period ) : '';
        if ( !empty($period) ) {
            $search_condition = '>=';
        }

        // set uid
        $uid = $uid ?? '';

        // set searchWord
        $searchWord = $searchWord ?? '';
        $searchWord = ! empty( $searchWord ) ? strtolower( (string) $searchWord ) : '';

        // set sortField
        $sortField = $sortField ?? 'id';

        $call_list = \DB::table( 'report_missed_calls' )
                        ->whereDate('time_start', $search_condition, $dateStart)
                        ->orderBy( $sortField, $sortBy )
                        ->get();

        $calls_cnt   = count( $call_list );
        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + ( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1;

        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
    }

    /**
     * get Diagrama list
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getDiagramList($dateStart = null, $period = null): array
    {
        return (new ReportMissedGraph())->getList($dateStart, $period);
    }

    /**
     * Format Call List
     * @param $data
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
}
