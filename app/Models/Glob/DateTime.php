<?php


namespace App\Models\Glob;


class DateTime
{
    public function getDateTime()
    { // под канаду.
        return date('Y-m-d H:i:s', time()-4*3600);
    }
}
