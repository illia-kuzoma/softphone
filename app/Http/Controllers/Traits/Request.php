<?php


namespace App\Http\Controllers\Traits;

trait Request
{
    /**
     * Receives keys from string request as array of keys.
     *
     * @param $data
     * @return array
     */
    private function _getIdsAsArray($data)
    {
        $a_id = [];

        if($data == '-' || !$data)
        {

        }
        elseif(is_string($data))
        {
            $a_id = explode(',', $data);
            if(count($a_id) > 1)
            {
                // Значит фильтруем по несольким ключам (пользователи, комманды, отделы ...)
            }
            elseif(count($a_id) == 1){
                $a_id = [(int)$a_id[0]];
            }
        }
        elseif(is_int($data))
            $a_id = [$data];
        elseif(is_array($data))
            return $data;

        return $a_id;
    }
}
