服务主进程启动事件
------

```
function onStart(\swoole_server $server);
```

Server启动在主进程的主线程回调此函数

在此事件之前Swoole Server已进行了如下操作

- 已创建了manager进程
- 已创建了worker子进程
- 已监听所有TCP/UDP端口
- 已监听了定时器

接下来要执行

- 主Reactor开始接收事件，客户端可以connect到Server

onStart事件在Master进程的主线程中被调用。onStart回调中，仅允许echo、打印Log、修改进程名称。不得执行其他操作。onWorkerStart和onStart回调是在不同进程中并行执行的，不存在先后顺序

> 注意: 在onStart中创建的全局资源对象不能在worker进程中被使用，因为发生onStart调用时，worker进程已经创建好了。

> 新创建的对象在主进程内，worker进程无法访问到此内存区域。

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