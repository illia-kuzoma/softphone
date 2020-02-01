<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Validator;

class User extends Model
{
    /**
     * @var
     */
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
     * @param $email
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getData($email)
    {
        $getUserByEmail = \DB::table( 'users' )->where( 'email', $email  )->first();
        $this->user     = $getUserByEmail;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'uid'        => $this->user->id,
            'token'      => $this->user->token,
            'photo_url'  => $this->user->photo,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'role'       => $this->user->role,
            'full_name'  => $this->user->first_name . ' ' . $this->user->last_name
        ];

        return ( $data );
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
     * Insert new User
     * @param $userData
     */
    public function insert($userData): void
    {
        if ( $this->isMultipleArray($userData) ) {
            $this->insertMultipleUserData($userData);
        } else {
            $this->insertSingleUserData($userData);
        }
    }

    /**
     * Validate Before Insert
     * @param $userData
     *
     * @return bool
     */
    public function validateBeforeInsert( $userData ): bool
    {
        $validator = Validator::make(
            [
                'email'      => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name'  => $userData['last_name'],
                'password'   => $userData['password'],
                'role'       => $userData['role'],
                'token'      => $userData['token'],
                'photo'      => $userData['photo'],
                'date_login' => $userData['date_login'],
            ],
            [
                'email'      => 'required|email|unique:users',
                'first_name' => 'required|max:20',
                'last_name'  => 'required|max:20',
                'password'   => 'required|min:6|max:32',
                'role'       => 'required|min:3',
                'token'      => 'required|max:256',
                'photo'      => 'max:256',
                'date_login' => 'date_format:Y-m-d H:i:s'
            ]
        );

        return ! $validator->fails();
    }

    /**
     * Insert Single User Data to DB
     * @param $singleUserData
     */
    public function insertSingleUserData($singleUserData): void
    {
        if ( $this->validateBeforeInsert( $singleUserData ) ) {
            DB::table( 'users' )->insert(
                [
                    'email'      => $singleUserData['email'],
                    'first_name' => $singleUserData['first_name'],
                    'last_name'  => $singleUserData['last_name'],
//                    'password'   => Hash::make($singleUserData['password']),
                    'password'   => $singleUserData['password'],
                    'role'       => $singleUserData['role'],
                    'token'      => $singleUserData['token'],
                    'photo'      => $singleUserData['photo'],
                    'date_login' => $singleUserData['date_login'],
                    'created_at' => date( 'Y-m-d H:i:s'),
                    'updated_at' => date( 'Y-m-d H:i:s'),
                ]
            );
        }
    }

    /**
     * Insert Multiple User Data to DB
     * @param $multipleUserData
     */
    public function insertMultipleUserData($multipleUserData): void
    {
        foreach ($multipleUserData as $singleUserData) {
            $this->insertSingleUserData($singleUserData);
        }
    }


}
