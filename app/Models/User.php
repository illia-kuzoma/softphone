<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    private $_fake_user_data = [
        'user_id' => 1,
        'photo_url' => 'https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg',
        'first_name' => 'Ivan',
        'last_name' => 'Petrov',
        'role' => 'user'
    ];

    /**
     * @param $uid
     * @return array
     */
    public function getData($uid = null): array
    {
        $data = $this->_fake_user_data;
        // .... logic
        return $data;
    }
}
