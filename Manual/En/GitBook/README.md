# EasySwoole
EasySwoole is a memory based PHP framework ,which is base on Swoole. EasySwoole is specially designed for API, and eliminates the performance penalty associated with traditional PHP running patterns in process arousal and file loading. And EasySwoole encapsulates Swoole but still keep all the characteristic of Swoole,so that developers can write multi process, asynchronous, highly available application services with minimal learning cost and effort. 

## Characteristic

- Efficient Develop
- Height Concurrency
- TCP\UDP Server
- Custom Event Loop
- Multi Process & Async Task
- Millisecond Timer

## About ab 'hello' Test
```
System: CentOS 7.1 
CPU: E5-2682
Memory: 1G
php: 5.6.30
Swoole: 1.9.17

Server Software:        easySwoole
Server Hostname:        127.0.0.1
    
Server Port:            9501
Document Path:          /
Document Length:        20 bytes
    
Concurrency Level:      500
Time taken for tests:   30.268 seconds
Complete requests:      500000
Failed requests:        0
Write errors:           0
Total transferred:      97500000 bytes
HTML transferred:       10000000 bytes
Requests per second:    16519.16 [#/sec] (mean)
Time per request:       30.268 [ms] (mean)
Time per request:       0.061 [ms] (mean, across all concurrent requests)
Transfer rate:          3145.74 [Kbytes/sec] received
    
Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0   15   1.0     15      25
Processing:     2   15   1.3     15      37
Waiting:        1   12   2.0     12      31
Total:         17   30   1.2     30      52
    
Percentage of the requests served within a certain time (ms)
   50%     30
   66%     30
   75%     31
   80%     31
   90%     31
   95%     31
   98%     33
   99%     34
   100%     52 (longest request)
```