<?php

namespace App\Adaptor;

interface Cache
{
    /**
     * @return self
     */
    public static function create();

    public function increment(): int;
}
