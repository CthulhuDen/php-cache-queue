<?php

namespace App\Adaptor\Cache;

use App\Adaptor\Cache;

class Fake implements Cache
{
    public static function create()
    {
        return new self();
    }

    public function increment(): int
    {
        return 0;
    }
}
