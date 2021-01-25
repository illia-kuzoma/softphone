<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Request;
use App\Http\Controllers\Traits\Response;
use App\Http\Controllers\Traits\UserAuth;
use App\Models\User;

class ReportUnattended extends Controller
{
    use UserAuth;
    use Response;
    use Request;

    private const DIAGRAM_DATA = 'diagrama';
    private const CALLS_DATA = 'calls';
    private const TEAM_IDS = 'teams';

    /**
     * @param string $token Подтверждение что зпрос послан от авторизованного пользователя.
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $uids Строка ключей агентов, через запятую.
     * @param bool $refresh Флаг, указывающий нужно ли подтянуть свежие данные с ЗОХО.
     * @return string
     */
    private function _getAll($token, $dateStart=null, $period=null, $uids=null, $refresh = false): string
    {
        $user = $this->getUser($token);
        if($user instanceof User)
        {
            $a_agent_id = [];

            $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);

            if($refresh) // Подтянуть данные с Зохо.
                $unattendedCalls->loadFromRemoteServer();

            $out = array_merge($this->getResponse($user, $a_agent_id), [
                self::CALLS_DATA => $unattendedCalls->getCallList($dateStart, $period, null,null,null,null),
                self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
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
     * @throws \Exception
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
     * @param string|null $searchWord Строка для поиска по значеням в БД.
     * @param string|null $sortField Строка содержащая поле сортировки.
     * @param string $sortBy Строка содержащая направление сортировки.
     * @param int $page Номер страницы.
     * @return string
     */
    public function getCalls($dateStart=null, $period=null, $departments=null, $teams=null, $uid=null, $searchWord=null, $sortField=null, $sortBy='DESC', $page = 1): string
    {
        $a_department_id = $this->_getIdsAsArray($departments);
        $a_team_id = $this->_getIdsAsArray($teams);
        $a_agent_id = $this->_getIdsAsArray($uid);

        list($a_department, $a_team, $a_agent_id) = $this->getTeamAndDepartmentList($a_department_id, $a_team_id, $a_agent_id);

        $unattendedCalls = new \App\Models\ReportUnattended($a_agent_id);
        $calls = $unattendedCalls->getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);

        $out = array_merge([
            self::DIAGRAM_DATA => $unattendedCalls->getDiagramList($dateStart, $period),
            self::CALLS_DATA => $calls,
            self::TEAM_IDS => $a_team,
            'departments' =>  $a_department
        ], $this->getAgentsArr($a_agent_id));
        return json_encode($out);
    }

    /**
     * Обновить данные в БД с серверов Зохо и получить данные с БД.
     *
     * @param string $token Подтверждение что зпрос послан от авторизованного пользователя.
     * @param string|null $dateStart Дата выборки.
     * @param string|null $period Период выборки.
     * @param string|null $uids Строка ключей агентов, через запятую.
     * @return string
     * @throws \Exception
     */
    public function getFreshData($token, $dateStart=null, $period=null, $uids=null): string
    {
        return $this->_getAll($token, $dateStart, $period, $uids, true);
    }


}
