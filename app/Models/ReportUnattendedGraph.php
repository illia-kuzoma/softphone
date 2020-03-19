<?php

namespace App\Models;

use Validator;

/**
 * Class ReportUnattendedGraph for work with graph of unattended calls
 * @package App\Models
 */
class ReportUnattendedGraph extends ReportUnattended
{
    use Common;

    /**
     * Table name.
     */
    public $table = "report_unattended_group";
    /**
     * Выбирает данные под диаграмму по указанному временному периоду.
     *
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getList($dateStart, $period): array
    {
        //echo $dateStart . " " . $period;
        [$dateFrom, $dateTo] = $this->getDateFromAndTo($this->getDateStart($dateStart), $this->getPeriod($period));
        //echo "<br>".$dateFrom. " " . $dateTo .  "<br>";exit;
        $graph_list = \DB::table( $this->table)->join('users', $this->table.'.agent_id', '=', 'users.id')
            ->whereBetween('day', [$dateFrom, $dateTo])
            ->orderBy('users.first_name')->get();
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
                if(isset($result[$item->agent_id]['calls_count']))
                {
                    $result[$item->agent_id]['calls_count'] = $result[$item->agent_id]['calls_count'] + $item->count;
                }
                else
                {
                    $result[$item->agent_id] = [
                        'uid' => $this->_getIdVal($item),
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'calls_count'=> $item->count,
                        'full_name' => $item->first_name . ' ' . $item->last_name
                    ];
                    /*$result[$item->agent_id] = array_merge(
                        $result[$item->agent_id],
                        ( new User() )->getUserData( $item->agent_id ) // TODO need to optimization in One request with all agent_ids.
                    );*/
                }
            }
        }
        return array_values($result);
    }

    /**
     * Insert new CallGraph
     * @param $callDataGraph
     */
    public function insert($callDataGraph): void
    {
        if(empty($callDataGraph)){
            return;
        }
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
                'count'      => $callDataGraph['count'],
                'order'      => $callDataGraph['order'],
                'agent_id'    => $callDataGraph['agent_id'],
                'day'        => $callDataGraph['day']
            ],
            [
                'count'      => 'integer',
                'order'      => 'integer',
                'agent_id'    => 'required|digits_between:10,19',
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
                \DB::table( $this->table )->updateOrInsert(
                    [
                        'day'        => $singleCallDataGraph['day'],
                        'agent_id'    => $singleCallDataGraph['agent_id']
                    ],
                    [
                        'count'      => $singleCallDataGraph['count'],
                        'order'      => $singleCallDataGraph['order'],
                        'agent_id'    => $singleCallDataGraph['agent_id'],
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
    public function updateDB()
    {
        \DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $sql = 'select report_unattended_call.agent_id, count(report_unattended_call.id) as count, DATE_FORMAT((report_unattended_call.time_start),"%Y-%m-%d") as day from report_unattended_call GROUP BY report_unattended_call.agent_id, DATE_FORMAT(report_unattended_call.time_start,"%Y %M %d");';
        $grouped_data_by_days = \DB::select(
            $sql,
            []
        );
        if(!empty($grouped_data_by_days))
        {
            $this->insert($grouped_data_by_days);
        }
    }
}
