<?php

require __DIR__ . '/vendor/autoload.php';

$client = \App\Adaptor\Queue\Redis::create();

$counter = 0;
for (;;) {
    if ($client->pop() !== null) {
        $counter += 1;
    } elseif ($counter > 0) {
        echo "Processed {$counter} entries\n";
        $counter = 0;
    }
}
