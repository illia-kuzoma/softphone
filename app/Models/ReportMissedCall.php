<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMissedCall extends Model
{
    const PAGES_PER_PAGE = 20;

    private $_fake_data_diagrama = [
        [
            'uid' => '1',
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'calls_count' => 0,
        ],
        [
            'uid' => '2',
            'first_name' => 'Ivan2',
            'last_name' => 'Petrov',
            'calls_count' => 4,
        ]
    ];

    private $_fake_data_call_list = [
        [
            'uid' => '1',
            'photo_url' => 'https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg',
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'business_name' => 'Bavaria Motors LLC',
            'contact' => 'UFO',
            'priority' => 'low',
            'phone' => '+380508008080',
            'time_create' => '1579996800',
        ],
        [
            'uid' => '2',
            'photo_url' => 'https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg',
            'first_name' => 'Ivan2',
            'last_name' => 'Petrov',
            'business_name' => 'Bavaria Motors LLC',
            'contact' => 'UFO',
            'priority' => 'low',
            'phone' => '+380508008082',
            'time_create' => '1579996802',
        ]
    ];

    /**
     * @param $dateStart
     * @param $period
     * @param $uid
     * @param $searchWord
     * @param $sortField
     * @param $sortBy
     * @param $page
     * @return array
     */
    public function getList($dateStart = null, $period = null, $uid = null,
                            $searchWord = null, $sortField = null, $sortBy = null,
                            $page = 1): array
    {

        #print_R(self::all());exit;
        $calls_cnt = count($this->_fake_data_call_list);
        $pages_count = floor($calls_cnt / self::PAGES_PER_PAGE) + ($calls_cnt%self::PAGES_PER_PAGE)===0?0:1;
        $page = $page; // $page может не быть той же что передал чел.
        $data = [
            'data' => $this->_fake_data_call_list,
            'pages_count' => $pages_count,
            'page' => $page
        ];
        // .... logic
        return $data;
    }

    /**
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getDiagramaList($dateStart = null, $period = null): array
    {
        $data = $this->_fake_data_diagrama;
        // .... logic
        return $data;
    }
}
