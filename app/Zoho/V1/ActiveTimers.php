<?php


namespace App\Zoho\V1;


class ActiveTimers extends ZohoV1
{
    public function getAll($department_id): array
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/myActiveTimers", 'departmentId='.$department_id.'?from=0&limit=10',
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


    public function getAll1($department_id): array
    {
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/timeTrackSettings/history", 'depId='.$department_id.'&include=owner',
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
    public function getAll2($agent_id = 102325000021403205): array
    {
        $module = 'tickets'; // tasks
        $a_results = $this->request(
            "https://desk.zoho.com/api/v1/agents/$agent_id/timeEntry", 'module='.$module.'&from=0&limit=10&include=owner&createdTimeRange=2020-06-16T06:00:00.000Z,2020-06-30T20:00:00.000Z&modifiedTimeRange=2020-06-16T06:00:00.000Z,2020-06-30T20:00:00.000Z',
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

    public function getDataArr()
    {
    }
}
