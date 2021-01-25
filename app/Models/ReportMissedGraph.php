<?php

namespace App\Models;

use Validator;

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
                        'uid' => $this->_getIdVal($item),
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
     * Insert new CallGraph
     * @param $callDataGraph
     */
    public function insert($callDataGraph): void
    {
        if ( $this->isMultipleArray($callDataGraph) ) {
            $this->insertMultipleCallDataGraph($callDataGraph);
        } else {
            $this->insertSingleCallDataGraph($callDataGraph);
        }
    }

    /**
     * Validate Before Insert
     * @param $callDataGraph
     *
     * @return bool
     */
    public function validateBeforeInsert( $callDataGraph ): bool
    {
        $validator = Validator::make(
            [
                'first_name' => $callDataGraph['first_name'],
                'last_name'  => $callDataGraph['last_name'],
                'count'      => $callDataGraph['count'],
                'order'      => $callDataGraph['order'],
                'user_id'    => $callDataGraph['user_id'],
                'day'        => $callDataGraph['day']
            ],
            [
                'first_name' => 'required|max:20',
                'last_name'  => 'required|max:20',
                'count'      => 'integer',
                'order'      => 'integer',
                'user_id'    => 'required|integer',
                'day'        => 'date_format:'.self::DATE_DAY_FORMAT
            ]
        );

        if($validator->fails()){
            print_r($validator->errors());
        }
        return ! $validator->fails();
    }

    /**
     * Insert Single CallDataGraph to DB
     * @param $singleCallDataGraph
     */
    public function insertSingleCallDataGraph($singleCallDataGraph): void
    {
        if($singleCallDataGraph && !is_array($singleCallDataGraph)){
            $singleCallDataGraph = json_decode(json_encode($singleCallDataGraph), true);
        }
        if(is_array($singleCallDataGraph))
        {
            $singleCallDataGraph['order']= $singleCallDataGraph['order']??0;
            if ( $this->validateBeforeInsert($singleCallDataGraph) ) {
                \DB::table( 'report_missed_graphs' )->updateOrInsert(
                    [
                        'day'        => $singleCallDataGraph['day'],
                        'user_id'    => $singleCallDataGraph['user_id']
                    ],
                    [
                        'first_name' => $singleCallDataGraph['first_name'],
                        'last_name'  => $singleCallDataGraph['last_name'],
                        'count'      => $singleCallDataGraph['count'],
                        'order'      => $singleCallDataGraph['order'],
                        'user_id'    => $singleCallDataGraph['user_id'],
                        'day'        => $singleCallDataGraph['day'],
                        'created_at' => date( self::DATE_TIME_FORMAT ),
                        'updated_at' => date( self::DATE_TIME_FORMAT ),
                    ]
                );
            }
        }
    }

    /**
     * Insert Multiple CallDataGraph to DB
     * @param $multipleCallDataGraph
     */
    public function insertMultipleCallDataGraph($multipleCallDataGraph): void
    {
        foreach ($multipleCallDataGraph as $singleCallDataGraph) {
            $this->insertSingleCallDataGraph($singleCallDataGraph);
        }
    }
}
