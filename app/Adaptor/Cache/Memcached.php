<?php

namespace App\Adaptor\Cache;

use App\Adaptor\Cache;

class Memcached implements Cache
{
    private $client;

    private function __construct(\Memcached $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $memcached = new \Memcached('spdtst/general');
        if (!$memcached->getOption(\Memcached::OPT_BINARY_PROTOCOL)) {
            // Who the fuck thought this was good idea to not really implement one-time initialization hook?
            // I hope to hell OPT_BINARY_PROTOCOL will never be default
            $memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $memcached->addServer('127.0.0.1', 11211);
        }
        return new self($memcached);
    }

    public function increment(): int
    {
        return $this->client->increment('visits', 1, 0);
    }
}
