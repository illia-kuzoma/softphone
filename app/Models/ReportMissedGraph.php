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
        if($dateStart == '-' || !$dateStart){
            $dateStart = date('Y-m-d H:i:s');
        }
        $dateStart = $dateStart ?$dateStart : date('Y-m-d H:i:s');
        $dateStart = ! empty( $dateStart ) ? date( 'Y-m-d H:i:s', strtotime( $dateStart ) ) : '';

        // set period
        if($period == '-' || !$period || !in_array($period,['day','week','month','year'])){
            $period = 'day';
        }
        $period = ! empty( $period ) ? strtolower( (string) $period ) : '';

        $graph_list = \DB::table( 'report_missed_graphs' )/*->where( 'day', '>=', $dateStart )*/->orderBy('first_name')->get();

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
                $result[] = [
                    'uid' => $item->user_id,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name,
                    'calls_count' => $item->count,
                    'full_name' => $item->first_name . ' ' . $item->last_name,
                ];
            }
        }

        return $result;
    }
}
