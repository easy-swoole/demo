# EasySwoole
```
  ______                          _____                              _        
 |  ____|                        / ____|                            | |       
 | |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___ 
 |  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
 | |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
 |______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                          __/ |                                               
                         |___/                                                
```
EasySwoole is a distributed, persistent memory PHP framework based on the Swoole extension. It was created specifically for APIs to get rid of the performance penalties associated with process calls and file loading.
EasySwoole highly encapsulates the Swoole Server and still maintains the original features of the Swoole server, supports simultaneous monitoring of HTTP, custom TCP, and UDP protocols, allowing developers to write multi-process, asynchronous, and highly available applications with minimal learning cost and effort.

## Common functions and components
- HTTP Controllers and Custom Routes
- TCP, UDP, WEB_SOCKET controllers
- Multiple communication protocols
- Asynchronous client and coroutine object pool
- Asynchronous processes, custom processes, timers
- Cluster distributed support, such as cluster node communication, service discovery, RPC
- Fully open system event registrar with EventHook

## About http ab test
test controller :
```php
<?php

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        $this->response()->write('Hello World');
    }
}
```
ab -c 100 -n 500000 http://172.18.95.34:9501/

```bash
This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 172.18.95.34 (be patient)
Completed 50000 requests
Completed 100000 requests
Completed 150000 requests
Completed 200000 requests
Completed 250000 requests
Completed 300000 requests
Completed 350000 requests
Completed 400000 requests
Completed 450000 requests
Completed 500000 requests
Finished 500000 requests


Server Software:        swoole-http-server
Server Hostname:        172.18.95.34
Server Port:            9501

Document Path:          /
Document Length:        63 bytes

Concurrency Level:      100
Time taken for tests:   41.405 seconds
Complete requests:      500000
Failed requests:        0
Non-2xx responses:      500000
Total transferred:      119000000 bytes
HTML transferred:       31500000 bytes
Requests per second:    12075.71 [#/sec] (mean)
Time per request:       8.281 [ms] (mean)
Time per request:       0.083 [ms] (mean, across all concurrent requests)
Transfer rate:          2806.66 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   0.5      1       4
Processing:     2    7   2.4      7      66
Waiting:        1    6   2.4      6      66
Total:          3    8   2.4      8      67

Percentage of the requests served within a certain time (ms)
  50%      8
  66%      9
  75%      9
  80%      9
  90%     10
  95%     10
  98%     11
  99%     12
 100%     67 (longest request)
```
> base on one core and 1G Ram
