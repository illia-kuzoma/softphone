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
        return (new ReportAgentStatusesGroup($this->getAgentIdFilter(),$this->getTypeNameFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }

    public function getStatusTotalList($dateStart, $period, $searchWord, $sortField, $sortBy, $page): array
    {
        return (new ReportAgentTotalStatus($this->getAgentIdFilter(),$this->getTypeNameFilter()))->getList($dateStart, $period, $searchWord, $sortField, $sortBy, $page);
    }

    public function getDiagramList($dateStart = null, $period = null): array
    {
        return (new ReportAgentTotalStatus($this->getAgentIdFilter(),$this->getTypeNameFilter()))->getGraphList($dateStart, $period);
    }

    /**
     * Исключенный статус.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    protected function except(\Illuminate\Database\Eloquent\Builder &$query, $field_name)
    {
        $query->where($this->table.'.'.$field_name, '!=', 'presence_status');
    }
}
