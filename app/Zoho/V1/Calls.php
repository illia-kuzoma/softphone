<?php


namespace App\Zoho\V1;


class Calls extends ZohoV1
{
    public function getOne($call_id): array
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/calls/$call_id", 'include=tickets,contacts,assignee,teams',
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        print_r($a_results);exit;
        if(empty($a_results))
        {
            throw new \Exception(sprintf("Departments list is empty!"));
        }
        $this->checkResponse($a_results);
        return $a_results;
    }
}
