<?php

require __DIR__ . '/vendor/autoload.php';

$ctx = new ZMQContext(1);

$resp = new ZMQSocket($ctx, ZMQ::SOCKET_PULL);
$resp->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
$resp->bind('ipc:///tmp/zmq.sock');

$counter = 0;
for (;;) {
    if ($resp->recv() !== false) {
        $counter += 1;
    } elseif ($counter > 0) {
        echo "Processed {$counter} entries\n";
        $counter = 0;
    }
}
