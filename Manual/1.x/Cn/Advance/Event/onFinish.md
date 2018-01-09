任务完成事件
------

```
function onFinish(\swoole_server $server, $taskId,$callBackObj);
```

当worker进程投递的任务在task_worker中完成时将触发本事件

> task进程的onTask事件中没有调用finish方法或者return结果，worker进程不会触发onFinish

> 执行onFinish逻辑的worker进程与下发task任务的worker进程是同一个进程

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