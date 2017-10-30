服务主进程退出事件
------

```
function onShutdown(\swoole_server $server);
```

此事件在Server结束时发生

在此事件之前Swoole Server已进行了如下操作

- 已关闭所有线程
- 已关闭所有worker进程
- 已close所有TCP/UDP监听端口
- 已关闭主Rector

> 注意: 强制kill进程不会触发此事件，如kill -9，需要使用kill -15来发送SIGTREM信号到主进程才能按照正常的流程终止

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