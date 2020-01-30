<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class User extends Model
{
    /**
     * @var
     */
    private $user;

    /**
     * @var array
     */
    private $notValidData = [];

    /**
     * @return array
     */
    public function getNotValidData(): array {
        return $this->notValidData;
    }

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
        $getUserByEmail = \DB::table( 'users' )->where( 'email', $email = 'support@wellnessliving.com' )->first();
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
            array(
                'email'      => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name'  => $userData['last_name'],
                'password'   => $userData['password'],
                'role'       => $userData['role'],
                'token'      => $userData['token'],
                'photo'      => $userData['photo'],
                'date_login' => strtotime( $userData['date_login'] ),
            ),
            array(
                'email'      => 'required|email|unique|max:32',
                'first_name' => 'max:20',
                'last_name'  => 'max:20',
                'password'   => 'required|min:6|max:32',
                'role'       => 'min:3',
                'token'      => 'max:256',
                'photo'      => 'mimes:jpeg,bmp,png',
                'date_login' => 'date'
            )
        );

        return ! $validator->fails();
    }

    /**
     * Insert Single User Data to DB
     * @param $singleUserData
     *
     * @return bool
     */
    public function insertSingleUserData($singleUserData): bool
    {
        if ( $this->validateBeforeInsert($singleUserData) ) {
            DB::table( 'users' )->insert(
                [
                    'email'      => $singleUserData['email'],
                    'first_name' => $singleUserData['first_name'],
                    'last_name'  => $singleUserData['last_name'],
                    'password'   => password_hash( $singleUserData['password'], PASSWORD_DEFAULT ),
                    'role'       => $singleUserData['role'],
                    'token'      => $singleUserData['token'],
                    'photo'      => $singleUserData['photo'],
                    'date_login' => $singleUserData['date_login'],
                    'created_at' => time(),
                    'updated_at' => time(),
                ]
            );

            return true;
        }

        return false;


//        DB::insert('insert into users (email, first_name, last_name, password, role, token, photo, date_login, created_at, updated_at)
//                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
//            [
//                $singleUserData['email'],
//                $singleUserData['first_name'],
//                $singleUserData['last_name'],
//                password_hash($singleUserData['password'], PASSWORD_DEFAULT),
//                $singleUserData['role'],
//                $singleUserData['token'],
//                $singleUserData['photo'],
//                $singleUserData['date_login'],
//                time(),
//                time()
//            ]
//        );
    }

    /**
     * Insert Multiple User Data to DB
     * @param $multipleUserData
     *
     * @return bool
     */
    public function insertMultipleUserData($multipleUserData): bool
    {
        foreach ($multipleUserData as $singleUserData) {
            if ( $this->validateBeforeInsert($singleUserData) ) {
                DB::table( 'users' )->insert(
                    [
                        'email'      => $singleUserData['email'],
                        'first_name' => $singleUserData['first_name'],
                        'last_name'  => $singleUserData['last_name'],
                        'password'   => password_hash( $singleUserData['password'], PASSWORD_DEFAULT ),
                        'role'       => $singleUserData['role'],
                        'token'      => $singleUserData['token'],
                        'photo'      => $singleUserData['photo'],
                        'date_login' => $singleUserData['date_login'],
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]
                );
            } else {
                $this->notValidData[] = $singleUserData;
            }
        }

        return empty($this->notValidData);
    }


}
