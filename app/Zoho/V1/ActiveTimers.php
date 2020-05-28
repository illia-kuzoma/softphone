<?php


namespace App\Zoho\V1;


class ActiveTimers extends ZohoV1
{
    public function getAll($department_id)
    {
        $departments = $this->request(
            "https://desk.zoho.com/api/v1/myActiveTimers", 'departmentId='.$department_id.'?from=0&limit=10',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        print_r($departments);exit;
        if(empty($departments))
        {
            throw new \Exception(sprintf("Departments list is empty!"));
        }
        $this->checkResponse($departments);
        return $departments;
    }

    public function getDataArr()
    {
    }
}
