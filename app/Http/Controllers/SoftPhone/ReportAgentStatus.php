<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Request;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;
use App\Models\ReportAgentStatuses;
use App\Models\User;

/**
 * Class ReportAgentStatus use to get agent/agents statuses in time line.
 * @package App\Http\Controllers\SoftPhone
 */
class ReportAgentStatus extends Controller
{
    use UserAuth;
    use Response;
    use Request;

    private const DIAGRAM_DATA = 'diagrama';
    private const STATUS_DATA = 'status';
    private const TEAM_IDS = 'teams';
    private const STATUS_TOTAL = 'total';

    /**
     * Первый запрос на все данные идёт сюда.
     *
     * @param string $token Подтверждение что зпрос послан от авторизованного пользователя.
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $uids Строка ключей агентов, через запятую.
     * @return string
     */
    private function _getAll($token, $dateStart=null, $period=null, $uids=null, $refresh = false): string
    {
        $user = $this->getUser($token);
        if($user instanceof User)
        {
            $a_agent_id = [];

            $o_statuses = new ReportAgentStatuses($a_agent_id);

            $out = array_merge($this->getResponse($user, $a_agent_id), [
                self::STATUS_TOTAL => $o_statuses->getStatusTotalList($dateStart, $period, null,'day',null,null),
                self::STATUS_DATA => $o_statuses->getStatusList($dateStart, $period, null,null,null,null),
                self::DIAGRAM_DATA => $o_statuses->getDiagramList($dateStart, $period),
                'types' => (new \App\Models\ReportAgentStatusesGroup())->getStatusNameList(),
            ]);

            return json_encode($out);
        }
        return $user;
    }

    /**
     * Первый запрос на все данные идёт сюда.
     *
     * @param string $token Подтверждение что зпрос послан от авторизованного пользователя.
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $uids Строка ключей агентов, через запятую.
     * @return string
     */
    public function getAll($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids);
    }

    /**
     * Запрос на получение части данных, согласно переданным параметрам.
     *
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $departments Строка ключей отделов, через запятую.
     * @param string|null $teams Строка ключей комманд, через запятую.
     * @param string|null $uid Строка ключей агентов, через запятую.
     * @param string|null $type Строка имён статусов, через запятую.
     * @param string|null $searchWord Строка для поиска по значеням в БД.
     * @param string|null $sortField Строка содержащая поле сортировки.
     * @param string $sortBy Строка содержащая направление сортировки.
     * @param int $page Номер страницы.
     * @return string
     */
    public function getPage($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $type=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);
        $a_type_id = $this->_getIdsAsArray($type, 'string');

        list($a_department, $a_team, $a_agent_id) = $this->getTeamAndDepartmentList($a_department_id, $a_team_id, $a_agent_id);

        $o_statuses = new ReportAgentStatuses($a_agent_id, $a_type_id);

        $out = array_merge([
            self::STATUS_DATA => $o_statuses->getStatusList($dateStart, $period, $searchWord, $sortField, $sortBy, $page),
            self::STATUS_TOTAL => $o_statuses->getStatusTotalList($dateStart, $period, $searchWord, $sortField, $sortBy, $page),
            self::TEAM_IDS => $a_team,
            self::DIAGRAM_DATA => $o_statuses->getDiagramList($dateStart, $period),
            'departments' =>  $a_department
        ], $this->getAgentsArr($a_agent_id));
        return json_encode($out);
    }

    /**
     * Запрос на получение части данных по диаграмме, согласно переданным параметрам.
     *
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $departments Строка ключей отделов, через запятую.
     * @param string|null $teams Строка ключей комманд, через запятую.
     * @param string|null $uid Строка ключей агентов, через запятую.
     * @param string|null $type Строка имён статусов, через запятую.
     * @param string|null $value Строка значений статусов, через запятую.
     * @return string
     */
    public function getChart($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $type=null, $value=null): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);
        $a_type_id = $this->_getIdsAsArray($type, 'string');
        $a_value_id = $this->_getIdsAsArray($value, 'string');

        $o_user = new User();
        // Получаю список агентов согласно департментам и командам.
        $a_agent_id_by_teams = $o_user->getIdArrByTeams($a_department_id, $a_team_id);

        if(empty($a_agent_id)) // Если агенты не указаны, тогда беру их согласно указанным отделам и командам.
            $a_agent_id = $a_agent_id_by_teams;

        $o_statuses = new ReportAgentStatuses($a_agent_id, $a_type_id, $a_value_id);

        $out = [
            self::DIAGRAM_DATA => $o_statuses->getDiagramList($dateStart, $period)
        ];
        return json_encode($out);
    }

    /**
     * Получение итоговых данных. Сумма всех однотипных статусов за выбранный период.
     * Если за Период Х в статусе У человек был 10 раз то все эти разы будут показаны как один раз ввиде сумы всех.
     *
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $departments Строка ключей отделов, через запятую.
     * @param string|null $teams Строка ключей комманд, через запятую.
     * @param string|null $uid Строка ключей агентов, через запятую.
     * @param string|null $type Строка имён статусов, через запятую.
     * @param string|null $searchWord Строка для поиска по значеням в БД.
     * @param string|null $sortField Строка содержащая поле сортировки.
     * @param string $sortBy Строка содержащая направление сортировки.
     * @param int $page Номер страницы.
     * @return string
     */
    public function getTotalPage($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $type=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);
        $a_type_id = $this->_getIdsAsArray($type, 'string');
        $o_user = new User();
        // Получаю список агентов согласно департментам и командам.
        $a_agent_id_by_teams = $o_user->getIdArrByTeams($a_department_id, $a_team_id);

        if(empty($a_agent_id)) // Если агенты не указаны, тогда беру их согласно указанным отделам и командам.
            $a_agent_id = $a_agent_id_by_teams;

        $o_statuses = new ReportAgentStatuses($a_agent_id, $a_type_id);

        $out = array_merge([
            self::STATUS_TOTAL => $o_statuses->getStatusTotalList($dateStart, $period, $searchWord, $sortField, $sortBy, $page),
        ], $this->getAgentsArr($a_agent_id));
        return json_encode($out);
    }
}
