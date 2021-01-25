<?php


namespace App\Http\Controllers\Traits;

trait Request
{
    /**
     * Receives keys from string request as array of keys.
     *
     * @param $data
     * @param string $to_type Type to convert data got from string.
     * @return array
     */
    private function _getIdsAsArray($data, $to_type='int')
    {
        $a_id = [];

        if($data == '-' || !$data)
        {

        }
        elseif(is_string($data))
        {
            $a_id = array_filter(explode(',', $data));
            if(count($a_id) > 1)
            {
                // Значит фильтруем по несольким ключам (пользователи, комманды, отделы ...)
            }
            elseif(count($a_id) == 1){
                switch($to_type)
                {
                    case 'int':
                        $a_id = [(int)$a_id[0]];
                        break;
                    case 'string':
                        $a_id = [(string)$a_id[0]];
                        break;
                    default:
                        $a_id = [$a_id[0]];

                }
            }
        }
        elseif(is_int($data))
            $a_id = [$data];
        elseif(is_array($data))
            return $data;

        return $a_id;
    }
}
