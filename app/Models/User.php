<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class User extends Model
{
    use Common;
    const ROLE_AGENT = 'agent';
    const ROLE_USER = 'user';

    public $table = "users";
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
        $getUserByEmail = \DB::table( $this->table)->where( 'email', $email  )->first();
        $this->user     = $getUserByEmail;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if(!$this->user){
            return [];
        }
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
        $users = \DB::table($this->table)->where('id', '=', $user_id)->get()->sortBy('id');
        $data = [];

        if (!empty($users)) {
            foreach ( $users as $user ) {
                $data['full_name']  = $user->first_name . ' ' . $user->last_name ;
                $data['photo_url']  = $user->photo;
                $data['first_name'] = $user->first_name;
                $data['last_name']  = $user->last_name;
            }
        }
        return $data;
    }

    /**
     * Insert new User
     * @param $userData
     */
    public function insert($userData): void
    {
        if(empty($userData)){
            return;
        }
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
                'id'      => $userData['user_id'],
                'email'      => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name'  => $userData['last_name'],
                /*'password'   => $userData['password'],*/
                'role'       => $userData['role'],
                /*'token'      => $userData['token'],*/
                'photo'      => $userData['photo'],
                'date_login' => $userData['date_login'],
            ],
            [
                'id'      => '',
                'email'      => 'email|unique:users',//required|email|unique:users
                'first_name' => 'required|max:20',
                'last_name'  => 'required|max:20',
                /*'password'   => 'max:32',*/
                'role'       => 'max:32',
                /*'token'      => 'max:32',*/
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
        if($singleUserData && !is_array($singleUserData)){
            $singleUserData = json_decode(json_encode($singleUserData), true);
        }
        if(is_array($singleUserData))
        {
            $singleUserData['email'] = ($singleUserData['role'] == User::ROLE_AGENT && empty($singleUserData['email']))?
                'rand'.(mt_rand(111,99999999)).User::ROLE_AGENT.'@ra'.mt_rand(55555,9999999999).'.nd':
                $singleUserData['email'];
            $singleUserData['date_login'] = $singleUserData['date_login']??date( 'Y-m-d H:i:s');

            if ( $this->validateBeforeInsert( $singleUserData ) ) {
                DB::table( $this->table )->updateOrInsert(['id'      => $singleUserData['user_id']],
                    [
                        'id'      => $singleUserData['user_id'],
                        'email'      => $singleUserData['email'],
                        'first_name' => $singleUserData['first_name'],
                        'last_name'  => $singleUserData['last_name'],
//                    'password'   => Hash::make($singleUserData['password']),
                        'password'   => $singleUserData['password']??'',
                        'role'       => $singleUserData['role']??'',
                        'token'      => $singleUserData['token']??'',
                        'photo'      => $singleUserData['photo']??'',
                        'date_login' => $singleUserData['date_login'],
                        'created_at' => date( 'Y-m-d H:i:s'),
                        'updated_at' => date( 'Y-m-d H:i:s'),
                    ]
                );
            }
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
    public static function getAllAgentIDs(): array
    {
        return self::query()->where('role', '=', User::ROLE_AGENT)->pluck('id')->toArray();
    }
    public static function getAllAgentIDFullName(): array
    {
        $res = [];
        $data = self::query()->where('role', '=', User::ROLE_AGENT)->select(['id', 'first_name', 'last_name'])->get();
        foreach($data as $datum){
            $res[] = [
                "text" => $datum->first_name ." ". $datum->last_name,
                "value" => $datum->id,
            ];
        }
        return $res;
    }

}
