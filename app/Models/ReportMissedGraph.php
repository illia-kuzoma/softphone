<?php

namespace App\Models;

class ReportMissedGraph extends ReportMissed
{
    /**
     * Выбирает данные под диаграмму по указанному временному периоду.
     *
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getList($dateStart, $period): array
    {
        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
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
