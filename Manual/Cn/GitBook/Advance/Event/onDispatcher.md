请求分发事件
------

```
function onDispatcher(Request $request,Response $response,$targetControllerClass,$targetAction);
```

HTTP请求进来后，easySwoole会对请求进行解析以及分发，当找到对应的控制器后将会执行本事件

> 注意: 如果请求无法解析到对应的控制器，或控制器不是继承自`AbstractController`将不会执行本事件

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
