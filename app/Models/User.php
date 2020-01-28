<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * @param null $user_id
     *
     * @return array
     */
    public function getData($user_id = null): array
    {
        $users = \DB::table('users')->get()->sortBy('id');
        $data = [];
        foreach ( $users as $user ) {
            $data[] = [
                'uid' => $user->id,
                'photo_url' => $user->photo,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role
            ];
        }
        if ( empty($data) ) {
            $data = $this->_fake_user_data;
        }

        // .... logic
        return ($data);
    }

    /**
     * getUserData
     * @param $user_id
     *
     * @return array
     */
    public function getUserData($user_id): array
    {
        $users = \DB::table('users')->where('id', '=', $user_id)->get()->sortBy('id');
        $data = [];

        if (!empty($users)) {
            foreach ( $users as $user ) {
                $data['full_name']  = $user->last_name . ' ' . $user->first_name;
                $data['photo_url']  = $user->photo;
                $data['first_name'] = $user->first_name;
                $data['last_name']  = $user->last_name;
            }
        }
        return $data;
    }
}
