<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class User extends Model
{
    use Common;
    const ROLE_AGENT = 'agent';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const STATUS_ACTIVE = 'ACTIVE';

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
        // TODO Проверка что время ханения токена не выйшло.
        // Токен может передавтаься только в теле POST запроса
        return true;
    }

    /**
     * @param $email
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function setUserData($email)
    {
        $getUserByEmail = \DB::table( $this->table)->where( 'email','=', $email  )->first();
        $this->user     = $getUserByEmail;
    }

    /**
     * That function can be used for auth in system without auth in ZOHO.
     *
     * @param $email
     * @param $pass
     * @throws Exception
     */
    public function getUserByLoginAndPass($email, $pass)
    {
        $this->setUserData($email);
        if($this->user){
            if($this->user->password != md5($pass)){
                throw new \Exception("User password is wrong!");
            }
        }
    }

    /**
     * Finds and set user by token.
     *
     * @param $token
     * @throws Exception
     */
    public function setUserByToken($token)
    {
        if(!$token || $token==='-')
        {
            throw new \Exception("User not authorized!");
        }
        $getUserByToken = \DB::table( $this->table)->where('token', '=', $token)->first();
        if(!$getUserByToken){
            throw new \Exception("User by token didn't exist!");
        }
        $this->user = $getUserByToken;
    }

    /**
     * Converts user object data to array.
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
     * Возвращает данные пользователя для отображения на екран.
     *
     * @param $user_id string Ключ пользователя.
     *
     * @return array
     */
    public function formatUserData($user_id): array
    {
        $a_user = (array)\DB::table($this->table)->where('id', '=', $user_id)->first();
        return self::prepareUserData($a_user);
    }

    /**
     * Подготавливает данные пользователя для отображения на екран.
     *
     * @param $a_user []
     * @return array
     */
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
        //print_r($userData);
        $validator = Validator::make(
            [
                'id'      => $userData['user_id'],
                'email'      => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name'  => $userData['last_name'],
                /*'password'   => $userData['password'],*/
                'role'       => $userData['role'],
                'zoho_role'       => $userData['zoho_role']??'', // Актуально только для агентов
                'status'       => $userData['status']??'', // Актуально только для агентов
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
                'zoho_role'       => 'max:50',
                'status'       => 'max:50',
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
                $singleUserData['email']:
                $singleUserData['email'];
            $singleUserData['date_login'] = $singleUserData['date_login']??date( 'Y-m-d H:i:s');

            if ( $this->validateBeforeInsert( $singleUserData ) ) {
                DB::table( $this->table )->updateOrInsert(['id' => $singleUserData['user_id']],
                    [
                        'id'      => $singleUserData['user_id'],
                        'email'      => $singleUserData['email'],
                        'first_name' => $singleUserData['first_name'],
                        'last_name'  => $singleUserData['last_name'],
//                    'password'   => Hash::make($singleUserData['password']),
                        'password'   => $singleUserData['password']??'',
                        'role'       => $singleUserData['role']??'',
                        'zoho_role'       => $singleUserData['zoho_role']??'',
                        'status'       => $singleUserData['status']??'',
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

    /**
     * Returns agent keys.
     *
     * @return array
     */
    public static function getAllAgentIDs(): array
    {
        return self::query()->where('role', '=', User::ROLE_AGENT)->pluck('id')->toArray();
    }

    /**
     * Возвращает массив пользователей. Где каждый элемент содержит имя и ИД пользователя.
     *
     * @param array $user_ids Ключи пользователей для выборки с БД.
     * @return array
     */
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

    /**
     * Gets primary key name in table.
     *
     * @return string
     */
    protected function _getIdKey(){
        return 'id';
    }

    /**
     * Gets primary key value.
     *
     * @return string
     */
    protected function _getIdVal(){
        return (string)$this->{$this->_getIdKey()}; //
    }

    /**
     * Обновить токен объкта по емейлу объекта.
     *
     * @param $s_token Новый токен.
     */
    public function updateToken($s_token)
    {
        \DB::table( $this->table)->where( 'email', $this->user->email  )
            ->update(['token'=>$s_token, 'date_login'=> Carbon::now()]);
        $this->user->token = $s_token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->user->token;
    }

    /**
     * Возвращает список ИД пользователей согласно переданным ИД отделов и команд.
     *
     * @param array $a_department_id Массив ИД отделов.
     * @param array $a_team_id Массив ИД комманд.
     * @return array
     */
    public function getIdArrByTeams($a_department_id = [], $a_team_id = [])
    {
        $query = self::query()->where('role', '=', User::ROLE_AGENT);
        if($a_team_id)
        {
            foreach($a_team_id as $k=>$_team_id){
                if($k == 0)
                    $query->where('team_id', 'LIKE', '%'.$_team_id.'%');
                else
                    $query->orWhere('team_id', 'LIKE', '%'.$_team_id.'%');
            }
        }
        elseif($a_department_id)
            $query->whereIn('department_id', $a_department_id);

        $data = $query->pluck('id')->toArray();

        return $data;
    }

    /**
     * Update or create user data.
     *
     * @param string $s_password
     * @param string $s_token May be empty in a case token didn't change.
     * @throws Exception
     */
    public function updateUser(string $s_password, string $s_token = '')
    {
        if($this->user)
        {
            $s_pass_md5 = md5($s_password);
            $a_update = ['date_login'=> Carbon::now(), 'password'=> $s_pass_md5];
            if($s_token)
            {
                $this->user->token = $a_update['token'] = $s_token;
            }
            \DB::table( $this->table)->where( 'email', $this->user->email  )
                ->update($a_update);
            $this->user->password = $s_pass_md5;
        }
        else{
            // пользователь создается вовремя обновления данных с Зохо. Если вошли сюда то пользователя ещё нет в БД.
            throw new Exception("User doesn't exist in DB" );
        }
    }

    /**
     * Checks password.
     *
     * @param $pass
     * @return bool
     */
    public function isPasswordCorrect($pass)
    {
        if($this->user->password != md5($pass)){
            throw new \Exception('Password not equal db password.' );
        }
        return true;
    }

    /**
     * Checks that log-in was not long time ago. Use to relog-in.
     *
     * @return bool
     */
    public function isOldAuth()
    {
        return (strtotime($this->user->date_login) + 60) < time();
    }

    /**
     * Check that current user has rights to enter without check on ZOHO side.
     *
     * @return bool
     */
    public function excluded()
    {
        if(in_array($this->user->email, ['Len@wellnessliving.com','Sasha@wellnessliving.com','paul.s@wellnessliving.com']))
        {
            return true;
        }
        return false;
    }

    /**
     * Checks access to log-in.
     * @throws Exception
     */
    private function checkUserRights()
    {
        if(($this->user->role == self::ROLE_AGENT && $this->user->status == self::STATUS_ACTIVE &&
            in_array($this->user->zoho_role,['Administrator', 'Team Lead'])) || $this->user->role == self::ROLE_ADMIN)
        {
            return;
        }
        $message = sprintf("User %s doesn't have rights to enter.", $this->user->email);
        \Log::notice($message);
        throw new Exception($message);
    }

    /**
     * Checks user to continue work with system.
     *
     * @throws Exception
     */
    public function checkUser()
    {
        if(!$this->user )
        {
            // Users, Agents and other actors are create when we update database by requesting ZOHO.
            throw new Exception("User doesn't exist in DB." );
        }
        return $this->checkUserRights();
    }

    public function getTeamAndDepartmentList(array $user_id): array
    {
        $query = self::query()->select(['department_id','team_id'])->whereIn('id', $user_id);

        $a_data = $query->get();
        $a_department = [];
        $a_team = [];
        foreach($a_data as $a_datum){
            if($a_datum->department_id)
            {
                $a_department[] = $a_datum->department_id;
            }

            if($a_datum->team_id)
            {
                $a_team = array_merge($a_team, explode(',', $a_datum->team_id));
            }
        }
        return [$a_department, $a_team];
    }
}
