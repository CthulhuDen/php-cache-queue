<?php

namespace App\Adaptor\Cache;

use App\Adaptor\Cache;
use Predis\Client;

class Predis implements Cache
{
    private $client;

    private function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function create()
    {
        $client = new Client(
            ['persistent' => 'spdtst/general'],
            ['profile' => '3.2']
        );

        return new self($client);
    }

    public function increment(): int
    {
        return $this->client->incr('visits');
    }
}
