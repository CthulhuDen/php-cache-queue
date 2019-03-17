<?php

namespace App\Adaptor\Queue;

use App\Adaptor\Queue;

class ZMQ implements Queue
{
    private $client;

    private function __construct(\ZMQSocket $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $pusher = new \ZMQSocket(
            new \ZMQContext(1),
            \ZMQ::SOCKET_PUSH, 'spdtst/queue/ipc',
            function (\ZMQSocket $socket) {
                $socket->connect('ipc:///tmp/zmq.sock');
            }
        );

        return new self($pusher);
    }

    public function push($item)
    {
        $this->client->send($item);
    }

    public function pop()
    {
        throw new \Exception('Doesnt make sense');
    }
}
