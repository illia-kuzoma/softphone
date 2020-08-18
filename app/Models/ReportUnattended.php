<?php

namespace App\Models;

use App\Zoho\V1\Department;
use App\Zoho\V1\UnattendedCalls;

class ReportUnattended extends ReportBase
{
    use \App\Models\Traits\Report;

    public function getSortableFields()
    {
        return [self::FIELD_TIME_CREATE, 'contact', 'first_name', 'business_name'];
    }

    public function loadFromRemoteServer()
    {
        // Делаю запросы к Зохо только если разница текущего врмеени и
        // последней созданной в БД записи больше 1го часа.
        // Не нужно часто дергать АПИ. Там есть лимиты https://www.zoho.com/recruit/api-new/api-limits.html
        $i_diff_dates = $this->diffNowAndLastCreation(ReportUnattendedCall::TABLE_NAME);
        $i_diff_unattended_dates = $this->diffNowAndLastCreation(ReportUnattendedGraph::TABLE_NAME);

        if($i_diff_dates > 3600 || $i_diff_unattended_dates > 3600){
            $max_time_start_call = $this->maxTimeStart(ReportUnattendedCall::TABLE_NAME);
            // Делаю выборку за день с существующего в БД. Поскольку в этот день выборка могла быть не полной.
            $o_uc = new UnattendedCalls(strtotime($max_time_start_call . " -1 day"));
            $a_agent_id = [];
            $o_users = new User();
            if($i_diff_dates > 10000)// С головы придуманное число. Идея в том что навряд ли агенты будут создаваться часто.
            {
                $o_department = new \App\Models\Department();
                $o_department_zoho = new Department();
                $a_department = $o_department_zoho->getDataArr();
                $o_department->insert($a_department);

                $a_uid_data = [];
                $o_team = new \App\Models\Team();
                $a_team = [];
                foreach($a_department as $item)
                {
                    $a_team_tmp = $o_department_zoho->getAllTeamDataArr($item['id']);
                    //print_r($a_team_tmp);
                    $a_team = array_merge($a_team, $a_team_tmp);
                    foreach($a_team_tmp as $team){
                        foreach($team['agents'] as $uid)
                        {
                            if(!isset($a_uid_data[$uid]))
                                $a_uid_data[$uid] = [
                                    'team_id' => [],
                                    'department_id' => $item['id']
                                ];

                            if(!isset($a_uid_data[$uid]['team_id'][$team['id']]))
                                $a_uid_data[$uid]['team_id'][$team['id']] = null;
                        }
                    }
                }
                $o_team->insert($a_team);

                $o_uc->setAAdditionalAgentsData($a_uid_data);
                $users_agent = $o_uc->getAgentsList();
                $a_agent_id = array_column($users_agent, 'user_id');
                $o_users->insert($users_agent);
            }else{
                $a_agent_id = User::getAllAgentIDs();
            }
            $a_agent_id = $this->filterUsers($a_agent_id);

            $o_calls = new ReportUnattendedCall();
            foreach($a_agent_id as $i_agent_id){
                $o_uc->setDataByAgent($i_agent_id);
                // In optimization purposes.
                $o_users->insert($o_uc->getAUsersClient());
                $o_uc->setAUsersClient([]);
                $o_calls->insert($o_uc->getAUnattended());
                $o_uc->setAUnattended([]);
            }

            (new ReportUnattendedGraph())->updateDB();
            (new ReportUnattendedCall())->updateDB();
        }
    }

    /**
     * get Diagram list
     * @param $dateStart
     * @param $period
     * @return array
     */
    public function getDiagramList($dateStart = null, $period = null): array
    {
        return (new ReportUnattendedGraph($this->getAgentIdFilter()))->getList($dateStart, $period);
    }

    public function getCallList($dateStart, $period, $searchWord, $sortField, $sortBy, $page): array
    {
        return (new ReportUnattendedCall($this->getAgentIdFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }
}
