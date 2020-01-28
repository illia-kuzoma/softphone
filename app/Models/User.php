<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * @param null $email
     *
     * @return array
     */
    public function getData($email): array
    {
        $getUserByEmail = \DB::table('users')->where('email', $email)->first();
        $data = [
            'uid' => $getUserByEmail->id,
            'photo_url' => $getUserByEmail->photo,
            'first_name' => $getUserByEmail->first_name,
            'last_name' => $getUserByEmail->last_name,
            'role' => $getUserByEmail->role,
            'full_name' => $getUserByEmail->first_name . ' ' . $getUserByEmail->last_name
        ];
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
