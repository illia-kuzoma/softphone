<?php
namespace App\Zoho\V1;

class Department extends ZohoV1
{
    /**
     * Получисть все включенные отделы.
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getAll()
    {
        $departments = $this->request(
            "https://desk.zoho.com/api/v1/departments", 'isEnabled=true',
            [
            'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($departments))
        {
            throw new \Exception(sprintf("Departments list is empty!"));
        }
        $this->checkResponse($departments);
        return $departments;
    }

    /**
     * @param $department_id
     * @return array|mixed
     * @throws \Exception
     */
    public function getOne($department_id)
    {
        $a_department = $this->request(
            "https://desk.zoho.com/api/v1/departments/$department_id", 'isEnabled=true',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_department))
        {
            throw new \Exception(sprintf("Department list is empty!"));
        }
        return $a_department;
    }

    /**
     * Получить только нужные данные по отделам.
     *
     * @return array
     * @throws \Exception
     */
    public function getDataArr()
    {
        $a_return = [];
        $departments = $this->getAll()['data'];
        foreach($departments as $department){
            $a_return[] = [
                'id' => $department['id'],
                'name' => $department['nameInCustomerPortal'],
                'description' => $department['description'],
            ];
        }
        return $a_return;
    }

    /**
     * Получить только нужные данные всех комманд отдела.
     *
     * @param $department_id integer ключ отдела.
     * @return array
     * @throws \Exception
     */
    public function getAllTeamDataArr($department_id)
    {
        $a_return = [];
        $a_teams = (new Team())->getAll($department_id)[Team::DATA_FIELD];
        foreach($a_teams as $team){
            $a_return[] = [
                'id' => $team['id'],
                'name' => $team['name'],
                'agents' => $team['agents'],
                'description' => $team['description'],
                'department_id' => $team['departmentId']
            ];
        }
        return $a_return;
    }
}
