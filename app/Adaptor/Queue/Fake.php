<?php

namespace App\Adaptor\Queue;

use App\Adaptor\Queue;

class Fake implements Queue
{
    public static function create()
    {
        return new self();
    }

    public function push($item)
    {
    }

    public function pop()
    {
        return null;
    }
}
