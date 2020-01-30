<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMissedGraph extends Model
{
    /**
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getList($dateStart, $period): array
    {
        // set dateStart
        if($dateStart == '-' || !$dateStart ||
            (date('Y-m-d', strtotime($dateStart)) != $dateStart)){
            $dateStart = date('Y-m-d');
        }
        else{
            $dateStart = date( 'Y-m-d', strtotime( $dateStart ) );
        }
        // set period
        if($period == '-' || !$period || !in_array($period,['day','week','month','year'])){
            $period = 'day';
        }
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

        $graph_list = \DB::table( 'report_missed_graphs' )
            ->where('day', '>=', $dateFrom)
            ->where('day', '<=', $dateTo)
            ->orderBy('first_name')->get();

        return $this->formatDataGraphList( $graph_list );
    }

    /**
     * Format Graph List
     * @param $data
     *
     * @return array
     */
    private function formatDataGraphList($data): array
    {
        $result = [];
        if ( ! empty( $data ) ) {
            foreach ( $data as $item ) {
                if(isset($result[$item->user_id]['calls_count']))
                {
                    $result[$item->user_id]['calls_count'] = $result[$item->user_id]['calls_count'] + $item->count;
                }
                else
                {
                    $result[$item->user_id] = [
                        'uid' => $item->user_id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'calls_count'=> $item->count,
                        'full_name' => $item->first_name . ' ' . $item->last_name,
                    ];
                }
            }
        }

        return array_values($result);
    }
}
