<?php

namespace App\Models;


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
}
