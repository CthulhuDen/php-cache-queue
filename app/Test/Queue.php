<?php

namespace App\Test;

use App\Test;

class Queue implements Test
{
    public static function create()
    {
        return new self();
    }

    public function run(...$arguments)
    {
        $driver = $arguments[0];

        $drivers = [
            'fake' => \App\Adaptor\Queue\Fake::class,
            'zmq' => \App\Adaptor\Queue\ZMQ::class,
            'redis' => \App\Adaptor\Queue\Redis::class,
            'predis' => \App\Adaptor\Queue\Predis::class,
        ];
        /* @var \App\Adaptor\Queue[] $drivers */

        if (!isset($drivers[$driver])) {
            throw new \Exception('Unknown driver');
        }

        $driver = $drivers[$driver];
        $client = $driver::create();
        $client->push('visit');
    }
}
