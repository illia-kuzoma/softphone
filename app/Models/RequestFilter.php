<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $text_name
 * @property string $text_functionality
 * @property string $day
 * @property string $text_period
 * @property string $text_department_id
 * @property string $text_team_id
 * @property string $text_user_id
 * @property string $text_status_type
 * @property string $s_chart_status
 * @property string $s_chart_phone_status
 * @property string $created_at
 * @property string $updated_at
 */
class RequestFilter extends Model
{
    /**
     * Эта группа фильтров для пропущенных звонков.
     */
    const FUNCTIONALITY_MISSED = 1;

    /**
     * Эта группа фильтров для функционала статусов.
     */
    const FUNCTIONALITY_STATUSES = 2;

    const TABLE_NAME = "request_filters";

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
    protected $fillable = ['text_name', 'text_functionality', 'day', 'text_period', 'text_department_id', 'text_team_id', 'text_user_id', 'text_status_type', 's_chart_status', 's_chart_phone_status', 'created_at', 'updated_at'];


    public function __construct()
    {
        $this->table = self::TABLE_NAME;
        parent::__construct();
    }


}
