<?php

namespace App\Adaptor;

interface Queue
{
    /**
     * @return self
     */
    public static function create();

    /**
     * @param mixed $item
     * @return null
     */
    public function push($item);

    /**
     * @return null|mixed
     */
    public function pop();
}
