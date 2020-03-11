<?php
namespace App\Zoho\V1;

class UnattendedCalls extends ZohoV1
{
    public function getAllCount($org_id, $from_time = '2019-01-01T07:47:43.206Z', $end_time = '2020-02-23T07:47:43.206Z', $from = 1, $limit = 99)
    {
        return $this->request(
            "https://desk.zoho.com/api/v1/sequentialUnattendedCalls/Count",
            'fromTime='.$from_time.'&endTime='.$end_time.'&from='.$from.'&limit='.$limit,
            [
                'orgId:' . $org_id
            ]
        );
    }

    public function getAll($org_id, $assignee_id, $from_time = '2019-10-01T07:47:43.206Z', $end_time = '2020-02-23T07:47:43.206Z', $from = 1, $limit = 99)
    {
        return $this->request(
            "https://desk.zoho.com/api/v1/sequentialUnattendedCalls",
            'assigneeId='.$assignee_id.'&fromTime='.$from_time.'&endTime='.$end_time.'&from='.$from.'&limit='.$limit,
            [
                'orgId:' . $org_id
            ]
        );
    }
}
