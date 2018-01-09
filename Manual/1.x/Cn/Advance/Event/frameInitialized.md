# 框架初始化后事件

```
function frameInitialized();
```

执行完`frameInitialize`事件后，框架开始检查并处理运行环境，在执行frameInitialized事件时，框架已经完成的工作有：

- frameInitialize事件
- 系统运行目录的检查与创建

运行目录的检查与创建包括了以下工作：

- 在`ROOT`目录下创建临时目录`Temp`
- 在`Temp`目录下创建会话存放目录`Session`
- 在`Temp`目录下创建日志存放目录`Log`

在此事件中，可以进行一些启动前的预处理，比如对IOC容器进行内容注入等操作，需要使用到上述目录的逻辑也可以放在本事件中完成

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