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
    public function getList($dateStart = '', $period = '', $uid = '',
                            $searchWord = '', $sortField = 'time_start', $sortBy = 'desc',
                            $page = 1): array
    {
        if($dateStart == '-' || !$dateStart ||
            (date('Y-m-d', strtotime($dateStart)) != $dateStart)){
            $dateStart = date('Y-m-d');
        }

        if($period == '-' || !$period || !in_array($period,['day','week','month','year'])){
            $period = 'day';
        }
        if($uid == '-' || !$uid){
            $uid = '';
        }
        if($searchWord == '-' || !$searchWord){
            $searchWord = '';
        }
        if(!in_array(strtolower($sortField),['time_start','contact', 'first_name', 'business_name'])){
            $sortField = 'time_start';
        }
        if(!in_array(strtolower($sortBy),['asc','desc'])){
            $sortBy = 'desc';
        }
        $page = (int)$page;
        if((int)$page<1)
        {
            $page = 1;
        }
       /* echo '$dateStart=' . $dateStart.'$period=' . $period.'$uid=' . $uid
            .'$searchWord=' . $searchWord.'$sortField=' . $sortField.'$sortBy=' . $sortBy
            .'$page=' . $page;exit;*/
        // set dateStart
        $dateStart = strtotime( $dateStart );

        $dateFrom = $dateTo = date( 'Y-m-d', $dateStart) ;
        // set period
        $currentDayOfWeek = date('w', $dateStart);
        switch (strtolower($period) ?? '') {
            case 'week':
                $dateFrom = (date( 'Y-m-d', strtotime( '-' . $currentDayOfWeek . ' days',  $dateStart ) ));
                $dateTo   = (date( 'Y-m-d', strtotime( '+' . ( 6 - $currentDayOfWeek ) . ' days', $dateStart ) ));
                break;
            case 'month':
                $dateFrom = (date('Y-m-d', strtotime('first day of this month', $dateStart) ));
                $dateTo = (date('Y-m-d', strtotime('last day of this month', $dateStart) ));
                break;
            case 'year':
                $dateFrom = (date('Y-m-d', strtotime('first day of January', $dateStart) ));
                $dateTo = (date('Y-m-d', strtotime('last day of December', $dateStart) ));
                break;
            case 'day':
            default:
               /* $dateFrom = $dateStart;
                $dateTo   = $dateStart;*/
                break;
        }
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';
        // set uid
        $uid = $uid ?? '';

        // set searchWord
        $searchWord = $searchWord ?? '';
        $searchWord = ! empty( $searchWord ) ? strtolower( (string) $searchWord ) : '';

       /* $pages = \DB::table( 'report_missed_calls' )->where(function($query) use($word){

        foreach($searchWords as $word){
            $query->orWhere('content', 'LIKE', '%'.$word.'%');
        }

    })->get();*/
        $call_list = \DB::table( 'report_missed_calls' )
            ->where('time_start', '>=', $dateFrom)
            ->where('time_start', '<=', $dateTo)->orderBy( $sortField, $sortBy )->offset(($page-1) * self::PAGES_PER_PAGE)->limit(self::PAGES_PER_PAGE)
            ->get();
        /*if($searchWord){
            foreach(['contact', 'first_name', 'business_name', 'last_name', 'phone'] as $field){
                $call_list->orWhere($field, 'LIKE', '%'.$searchWord.'%');
            }
        }*/

        $calls_cnt   = \DB::table( 'report_missed_calls' )->count();
        $pages_count = floor( $calls_cnt / self::PAGES_PER_PAGE ) + (( $calls_cnt % self::PAGES_PER_PAGE ) === 0 ? 0 : 1);

        return [
            'data'        => $this->formatDataCallList( $call_list ),
            'pages_count' => $pages_count,
            'page'        => $page
        ];
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
}
