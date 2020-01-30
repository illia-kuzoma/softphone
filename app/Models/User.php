<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    private $user;

    /**
     * Возвращает результат проверки токена.
     *
     * @return bool
     */
    public function checkToken()
    {
        // TODO проверка токена полученого от клиента на соответствие храниимому.
        // TODO Проверка что время ханения токена не выйшло.
        // Токен может передавтаься только в теле POST запроса.
        return true;
    }

    /**
     * @param $user
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getData($user)
    {
        $getUserByEmail = \DB::table('users')->where('email', $email = 'support@wellnessliving.com')->first();
        $this->user = $getUserByEmail;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'uid' => $this->user->id,
            'token' => $this->user->token,
            'photo_url' => $this->user->photo,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'role' => $this->user->role,
            'full_name' => $this->user->first_name . ' ' . $this->user->last_name
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
