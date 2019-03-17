<?php

namespace App\Adaptor\Cache;

use App\Adaptor\Cache;

class Redis implements Cache
{
    private $client;

    private function __construct(\Redis $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $client = new \Redis();
        $client->pconnect('127.0.0.1', 6379, 0, 'spdtst/general');
        $client->select(1);
        return new self($client);
    }

    public function increment(): int
    {
        return $this->client->incr('visits');
    }
}
