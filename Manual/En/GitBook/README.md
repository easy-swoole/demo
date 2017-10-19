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


Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9501

Document Path:          /
Document Length:        21 bytes

Concurrency Level:      100
Time taken for tests:   5.808 seconds
Complete requests:      50000
Failed requests:        0
Write errors:           0
Total transferred:      8850000 bytes
HTML transferred:       1050000 bytes
Requests per second:    8608.19 [#/sec] (mean)
Time per request:       11.617 [ms] (mean)
Time per request:       0.116 [ms] (mean, across all concurrent requests)
Transfer rate:          1487.94 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   0.4      1       2
Processing:     2   10   2.5     10      81
Waiting:        1   10   2.4      9      80
Total:          3   12   2.5     12      83

Percentage of the requests served within a certain time (ms)
  50%     12
  66%     12
  75%     12
  80%     12
  90%     12
  95%     13
  98%     13
  99%     13
 100%     83 (longest request)
```
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>