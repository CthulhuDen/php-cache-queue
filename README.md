## Cache/Queue in PHP: the Benchmarks

This is a PHP application with endpoints dedicated to calling either in-memory cache's increment method,
or pushing a message into a queue. The purpose is to measure the throughput of different cache/queue drivers
when used from PHP.

Suggested to be benchmarked by tool like `ab`/`wrk` or similar.

### Cache

`/cache/{driver}`, where `driver` in:

* `fake` — reference no-op implementation;
* `memcached` — uses `memcached` php extension;
* `redis` — uses `redis` php extension;
* `predis` — uses `predis` composer library (spoiler: slowest of them all).

Visiting either of those endpoints just increments the dedicated atomic counter.

### Queue

`/queue/{driver}`, where `driver` in:

* `fake` — reference no-op implementation;
* `zmq` — uses `zmq` php extension with `ipc:///tmp/zmq.sock` socket;
* `redis`— uses `redis` php extension;
* `predis` — uses `predis` composer library (once again, slowest of them all, as expected).

Visiting those endpoints will put simple short message into the appropriate queue for that driver.

If you worry about memory being filled with messages, you may want to run `redispopper.php`/`zmqserver.php`, although 
those may themselves contribute to slower main benchmark result.

### Results

Core i7-4710MQ laptop.

#### Cache

##### No-op
```
$ wrk -c20 -d10s -t10 http://spdtst.local/cache/fake
Running 10s test @ http://spdtst.local/cache/fake
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency   754.98us  597.76us  15.04ms   91.85%
    Req/Sec     2.90k   330.14     8.13k    86.13%
  289692 requests in 10.10s, 52.75MB read
Requests/sec:  28692.42
Transfer/sec:      5.23MB
```

##### Memcached
```
$ wrk -c20 -d10s -t10 http://spdtst.local/cache/memcached
Running 10s test @ http://spdtst.local/cache/memcached
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.01ms  785.11us  25.23ms   92.86%
    Req/Sec     2.12k   157.02     2.91k    77.18%
  212853 requests in 10.10s, 39.98MB read
Requests/sec:  21075.54
Transfer/sec:      3.96MB
```

##### Redis PHP extension
```
$ wrk -c20 -d10s -t10 http://spdtst.local/cache/redis
Running 10s test @ http://spdtst.local/cache/redis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.14ms  748.59us  17.82ms   91.52%
    Req/Sec     1.87k   107.27     2.31k    75.42%
  188192 requests in 10.10s, 35.35MB read
Requests/sec:  18632.96
Transfer/sec:      3.50MB
```

##### Redis PHP library
```
$ wrk -c20 -d10s -t10 http://spdtst.local/cache/predis
Running 10s test @ http://spdtst.local/cache/predis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.73ms    1.16ms  22.96ms   87.36%
    Req/Sec     1.23k   135.82     3.57k    89.12%
  123168 requests in 10.10s, 23.13MB read
Requests/sec:  12200.44
Transfer/sec:      2.29MB
```

The results are pretty clear.

#### Queue

##### Fake
```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/fake
Running 10s test @ http://spdtst.local/queue/fake
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency   849.70us    3.65ms 204.34ms   99.43%
    Req/Sec     2.91k   289.89     7.67k    78.02%
  290381 requests in 10.10s, 50.94MB read
Requests/sec:  28751.59
Transfer/sec:      5.04MB
```

##### ZeroMQ

First, I ran test without consumer so the messages just piled up in memory:

```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/zmq
Running 10s test @ http://spdtst.local/queue/zmq
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency   655.63us  521.67us  16.53ms   91.99%
    Req/Sec     2.65k   793.30     6.16k    81.91%
  24773 requests in 10.02s, 4.35MB read
Requests/sec:   2473.03
Transfer/sec:    444.26KB
```

Now I started the `zmqserver.php` on the same machine and ran the benchmark again:

```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/zmq
Running 10s test @ http://spdtst.local/queue/zmq
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     0.89ms  684.08us  20.00ms   91.55%
    Req/Sec     2.43k   239.07     5.24k    85.96%
  243132 requests in 10.10s, 42.65MB read
Requests/sec:  24074.36
Transfer/sec:      4.22MB
```

So, ZeroMQ performance in this particular modus operandi seems to degrade with amount of unprocessed messages,
though performance with fastly dispatched messages looks great.

##### Redis PHP extension

First, run without consumer:

```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/redis
Running 10s test @ http://spdtst.local/queue/redis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.14ms  793.77us  20.77ms   92.46%
    Req/Sec     1.88k   105.32     2.48k    72.52%
  189016 requests in 10.10s, 33.16MB read
Requests/sec:  18714.42
Transfer/sec:      3.28MB
```

Seems redis has no trouble keeping the messages in memory. No run in parallel with a consumer:

```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/redis
Running 10s test @ http://spdtst.local/queue/redis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.21ms    0.89ms  16.37ms   93.00%
    Req/Sec     1.79k   124.20     2.88k    77.44%
  179668 requests in 10.10s, 31.52MB read
Requests/sec:  17789.52
Transfer/sec:      3.12MB
```

In fact, the presence of the consumer concurrent to the producer on the same machine slightly reduced the performance.

##### Redis PHP library

```
Running 10s test @ http://spdtst.local/queue/predis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.99ms    1.25ms  25.55ms   85.06%
    Req/Sec     1.06k   118.90     3.27k    92.71%
  106208 requests in 10.10s, 18.63MB read
Requests/sec:  10517.58
Transfer/sec:      1.85MB
```

```
$ wrk -c20 -d10s -t10 http://spdtst.local/queue/predis
Running 10s test @ http://spdtst.local/queue/predis
  10 threads and 20 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     2.18ms    1.48ms  24.24ms   87.58%
    Req/Sec     0.99k   117.25     3.16k    91.52%
  98683 requests in 10.10s, 17.31MB read
Requests/sec:   9772.31
Transfer/sec:      1.71MB
```

Just as expected, redis php library is the slowest of them all.
