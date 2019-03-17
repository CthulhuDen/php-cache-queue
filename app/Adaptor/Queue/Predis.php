<?php

namespace App\Adaptor\Queue;

use App\Adaptor\Queue;
use Predis\Client;

class Predis implements Queue
{
    private $client;

    private function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $client = new Client(
            ['persistent' => 'spdtst/queue'],
            ['profile' => '3.2']
        );
        $client->select(1);

        return new self($client);
    }

    public function push($item)
    {
        $this->client->lpush('queue', $item);
    }

    /**
     * @return null|mixed
     */
    public function pop()
    {
        $ret = $this->client->brpop(['queue'], 1);
        return $ret ? $ret[1] : null;
    }
}
