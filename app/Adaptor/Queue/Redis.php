<?php

namespace App\Adaptor\Queue;

use App\Adaptor\Queue;

class Redis implements Queue
{
    private $client;

    private function __construct(\Redis $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $client = new \Redis();
        $client->pconnect('127.0.0.1', 6379, 0, 'spdtst/queue');
        $client->select(1);

        return new self($client);
    }

    public function push($item)
    {
        $this->client->lPush('queue', $item);
    }

    /**
     * @return null|mixed
     */
    public function pop()
    {
        $ret = $this->client->brPop(['queue'], 1);
        return $ret ? $ret[1] : null;
    }
}
