服务退出事件
------

```
function onWorkerStop(\swoole_server $server,$workerId);
```

此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源

- $workerId是一个从0-$worker_num之间的数字，表示这个worker进程的ID，$workerId和进程PID没有任何关系
- 进程异常结束，如被强制kill、致命错误、core dump 时无法执行onWorkerStop回调函数

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