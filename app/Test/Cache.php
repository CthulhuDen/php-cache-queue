<?php

namespace App\Test;

use App\Test;

class Cache implements Test
{
    public static function create()
    {
        return new self();
    }

    public function run(...$arguments)
    {
        $driver = $arguments[0];

        $drivers = [
            'fake' => \App\Adaptor\Cache\Fake::class,
            'memcached' => \App\Adaptor\Cache\Memcached::class,
            'redis' => \App\Adaptor\Cache\Redis::class,
            'predis' => \App\Adaptor\Cache\Predis::class,
        ];
        /* @var \App\Adaptor\Cache[] $drivers */

        if (!isset($drivers[$driver])) {
            throw new \Exception('Unknown driver');
        }

        $driver = $drivers[$driver];
        $client = $driver::create();
        echo "{$client->increment()}\n";
    }
}
