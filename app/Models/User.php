<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    private $_fake_user_data = [
        'uid' => '56651777',
        'photo_url' => 'https://contacts.zoho.com/file?ID=56651777&fs=thumb',
        'first_name' => 'Ivan',
        'last_name' => 'Petrov',
        'role' => 'user'
    ];

    /**
     * @param null $uid
     * @return array
     */
    public function getData($uid = null): array
    {
        $data = $this->_fake_user_data;
        // .... logic
        return ($data);
    }
}
