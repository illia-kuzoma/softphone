<?php

namespace App\Models;

class ReportAgentStatuses extends ReportBase
{
    use Traits\Report;

    public function getSortableFields()
    {
        return [self::FIELD_TIME_CREATE, 'contact', 'first_name', 'business_name'];
    }

    public function getStatusList($dateStart, $period, $searchWord, $sortField, $sortBy, $page): array
    {
        return (new ReportAgentStatusesGroup($this->getAgentIdFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }

    public function getStatusTotalList($dateStart, $period, $searchWord, $sortField, $sortBy, $page): array
    {
        return (new ReportAgentTotalStatus($this->getAgentIdFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }

}
