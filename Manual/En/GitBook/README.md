<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="ROBOTS" content="ALL"/>
    <meta name="MSSmartTagsPreventParsing" content="true" />
    <meta name="keywords" content="easySwoole|swoole框架|easySwoole文档" />
    <meta name="description" content="easySwoole框架文档，旨在提供一个高效、快速、优雅的框架给php开发者。" />
    <meta name="msapplication-TileColor" content="#113228">
    <title>easySwoole|swoole框架|使PHP开发变得快速、高效</title>
    <link rel="stylesheet" href="css/main.css">
</head>

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
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>