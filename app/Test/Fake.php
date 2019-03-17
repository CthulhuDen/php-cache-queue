<?php

namespace App\Test;

use App\Test;

class Fake implements Test
{
    public static function create()
    {
        return new self();
    }

    public function run(...$arguments)
    {
    }
}
