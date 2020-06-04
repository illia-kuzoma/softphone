<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class User extends Model
{
    use Common;
    const ROLE_AGENT = 'agent';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    public $table = "users";
    /**
     * @var User
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
        $getUserByEmail = \DB::table( $this->table)->where( 'email','=', $email  )->first();
        if(!$getUserByEmail){
            throw new \Exception("User didn't exist!");
        }
        $this->user     = $getUserByEmail;
    }

    public function getUserByLoginAndPass($email, $pass)
    {
        $this->getData($email);
        if($this->user->password != md5($pass)){
            throw new \Exception("User password is wrong!");
        }
    }

    public function getUserByToken($token)
    {
        if(!$token || $token==='-')
        {
            throw new \Exception("User not authorized!");
        }
        $getUserByToken = \DB::table( $this->table)->where('token', '=', $token)->first();
        if(!$getUserByToken){
            throw new \Exception("User by token didn't exist!");
        }
        $this->user     = $getUserByToken;
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
        $a_user = (array)\DB::table($this->table)->where('id', '=', $user_id)->first();
        return self::prepareUserData($a_user);
    }

    public static function prepareUserData($a_user): array
    {
        $data = [];
        $data['full_name']  = $a_user['first_name'] . ' ' . $a_user['last_name'] ;
        $data['photo_url']  = $a_user['photo'];
        $data['first_name'] = $a_user['first_name'];
        $data['last_name']  = $a_user['last_name'];
        $data['department']  = [
            'id' => $a_user['department_id']?''.$a_user['department_id']:'',
        ];

        // TODO to optimize selection from db for team and department tables.
        $data['department']['name'] = $a_user['department_id']?
            Department::query()->where(['id' => $a_user['department_id']])->select('name')->first()['name']:
            '';
        $data['team']  = ['id' =>$a_user['team_id']?''.$a_user['team_id']:''];
        $data['team']['name'] = '';
        if($a_user['team_id'])
        {
            $a_team_id = explode(',', $a_user['team_id']);
            $a_team_name = Team::query()->whereIn('id', $a_team_id)->pluck('name')->toArray();
            $data['team']['name'] = implode(', ', $a_team_name);
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
                //'team_id'      => $userData['team_id'],
                //'department_id'      => $userData['department_id'],
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
                //'team_id'      => '',
                //'department_id'      => '',
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
                        'team_id'      => $singleUserData['team_id']??null,
                        'department_id'      => $singleUserData['department_id']??null,
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
    public static function getAllAgentIDFullName($user_ids = []): array
    {
        $res = [];
        $query = self::query()->where('role', '=', User::ROLE_AGENT);
        if($user_ids)
            $query->whereIn('id', $user_ids);
        $query->select(['id', 'first_name', 'last_name']);
        $data = $query->get();
        /**
         * @var $datum self
         */
        foreach($data as $datum){
            $res[] = [
                "name" => $datum->first_name ." ". $datum->last_name,
                "value" => $datum->_getIdVal(),
            ];
        }
        return $res;
    }

    protected function _getIdKey(){
        return 'id';
    }

    protected function _getIdVal(){
        return (string)$this->{$this->_getIdKey()}; //
    }

    public function updateToken($s_token)
    {
        \DB::table( $this->table)->where( 'email', $this->user->email  )
            ->update(['token'=>$s_token, 'date_login'=> Carbon::now()]);
        $this->user->token = $s_token;
    }

    public function getToken()
    {
        return $this->user->token;
    }

    public function getIdArrByTeams($a_department_id = [], $a_team_id = [])
    {
        $query = self::query()->where('role', '=', User::ROLE_AGENT);
        if($a_team_id)
        {
            foreach($a_team_id as $k=>$_team_id){
                if($k == 0)
                {
                    $query->where('team_id', 'LIKE', '%'.$_team_id.'%');
                }
                else
                    $query->orWhere('team_id', 'LIKE', '%'.$_team_id.'%');
            }
        }
        elseif($a_department_id)
            $query->whereIn('department_id', $a_department_id);

        $data = $query->pluck('id')->toArray();

        return $data;
    }

}
