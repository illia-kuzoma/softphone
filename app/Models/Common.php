<?php


namespace App\Models;


trait Common
{
    /**
     * Check array Single or Multiple
     * @param $data
     *
     * @return bool
     */
    public function isMultipleArray( $data ): bool
    {
        $keys_count = count( array_keys( $data ) );

        if($keys_count > 1)
        {
            return true;
        }
        $first_key = $keys_count-1;

        if(!empty($data[$first_key]))
        {
            return true;
        }

        return false;
    }
}
