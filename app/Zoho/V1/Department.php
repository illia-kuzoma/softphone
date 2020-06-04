<?php
namespace App\Zoho\V1;

class Department extends ZohoV1
{
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
