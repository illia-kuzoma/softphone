<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissedCall extends Model
{
    /**
     * @var string
     */
    protected $table = 'report_missed_calls';

    /**
     * @param $dateStart
     * @param $dateEnd
     * @param $uid
     * @param $searchWord
     * @param $sortField
     * @param $sortBy
     * @return array
     */
    public function getCallList($dateStart, $dateEnd, $uid, $searchWord, $sortField, $sortBy): array
    {

    }

    /**
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getDiagramaList($dateStart, $dateEnd): array
    {

    }
}
