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
    public function getAll2($agent_id, $module='calls', $from=0, $limit=0, $created_time_from, $created_time_to): array
    {
        $createdTimeRange = $created_time_from.','.$created_time_to;
        //$module = 'calls'; // tasks  calls  tickets
        $a_results = $this->request(
            //"https://desk.zoho.com/api/v1/agents/$agent_id/timeEntry", 'module='.$module.'&from=0&limit=100&include=owner&modifiedTimeRange=2020-02-10T06:00:00.000Z,2020-06-30T20:00:00.000Z',
            "https://desk.zoho.com/api/v1/agents/$agent_id/timeEntry", 'module='.$module.'&from='.$from.'&limit='.$limit.'&include=owner&createdTimeRange='.$createdTimeRange,
            [
                'orgId:' . ((new Organization())->getIdWellnessliving())
            ]
        );
        if(empty($a_results))
        {
            throw new \Exception(sprintf("Departments list is empty!"));
        }
        $this->checkResponse($a_results);
        return $a_results['data'];
    }

    public function getDataArr()
    {
    }
}
