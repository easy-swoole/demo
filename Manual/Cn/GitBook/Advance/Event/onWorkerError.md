服务异常事件
------

```
function onWorkerError(\swoole_server $server,$worker_id,$worker_pid,$exit_code);
```

当worker/task_worker进程发生异常后会在Manager进程内回调此函数

- $worker_id是异常进程的编号
- $worker_pid是异常进程的ID
- $exit_code退出的状态码，范围是 1 ～255
- 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
- 通过记录日志或者发送报警的信息来提示开发者进行相应的处理。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>