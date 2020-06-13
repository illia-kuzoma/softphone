<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $department_id
 * @property string $created_at
 * @property string $updated_at
 */
class Team extends Model
{
    use Common;

    const TABLE_NAME = "teams";

    public $table = "";
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'description', 'department_id', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->table = self::TABLE_NAME;
        parent::__construct();
    }

    /**
     * Insert new Call
     * @param $callData
     */
    public function insert($callData): void
    {
        if(empty($callData)){
            return;
        }
        if ( $this->isMultipleArray($callData) ) {
            $this->insertMultipleCallData($callData);
        } else {
            $this->insertSingleCallData($callData);
        }
    }

    /**
     * Insert Multiple Call Data to DB
     * @param $multipleCallData
     */
    public function insertMultipleCallData($multipleCallData): void
    {
        foreach ($multipleCallData as $singleCallData) {
            $this->insertSingleCallData($singleCallData);
        }
    }

    /**
     * Insert Single Call Data to DB
     * @param $singleCallData
     */
    public function insertSingleCallData($singleCallData): void
    {
        if ( $this->validateBeforeInsert($singleCallData) ) {
            $res = \DB::table( $this->table )->updateOrInsert(['id' => $singleCallData['id']],
                [
                    'id'            => $singleCallData['id'],
                    'name'          => $singleCallData['name'],
                    'description'   => $singleCallData['description']?$singleCallData['description']:'',
                    'department_id' => $singleCallData['department_id'],
                ]
            );
        }
    }

    /**
     * Validate Before Insert
     * @param $callData
     *
     * @return bool
     */
    public function validateBeforeInsert( $callData ): bool
    {
        $validator = \Validator::make(
            [
                'id'            => $callData['id'],
                'name'          => $callData['name'],
                'description'   => $callData['description'],
                'department_id' => $callData['department_id'],
            ],
            [
                'id'            => 'required|digits_between:10,19', // required|string|unique:report_missed_calls|
                'name'          => 'max:100',
                'description'   => 'max:400',
                'department_id' => 'required|digits_between:10,19',
            ]
        );
        if($validator->fails()){
            print_r($validator->errors());exit();
        }
        return ! $validator->fails();
    }

    /**
     * Возвращает массив каоменда. Где каждый элемент содержит имя и ИД комаенды.
     *
     * @param array $a_department_id Список ИД отделов.
     * @param array $ids Список ИД команд.
     * @return array
     */
    public function getAllArr($a_department_id = [], $ids = [])
    {
        $res = [];
        $query = self::query();
        if($a_department_id)
            $query->whereIn('department_id', $a_department_id);
        if($ids)
            $query->whereIn('id', $ids);
        $query->select(['id', 'name']);
        $data = $query->get();
        /**
         * @var $datum self
         */
        foreach($data as $datum){
            $res[] = [
                "value" => ''.$datum->id,
                "name" => $datum->name,
            ];
        }
        return $res;
    }
}
