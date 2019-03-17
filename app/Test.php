<?php

namespace App;

interface Test
{
    /**
     * @return self
     */
    public static function create();

    public function run(...$arguments);
}
