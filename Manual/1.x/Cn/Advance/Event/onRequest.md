收到请求事件
------

```
function onRequest(Request $request,Response $response);
```

当easySwoole收到任何的HTTP请求时，均会执行该事件。该事件可以对HTTP请求全局拦截。
```
$sec = new Security();
if($sec->check($request->getRequestParam())){
   $response->write("do not attack");
   $response->end();
   return;
}
if($sec->check($request->getCookieParams())){
   $response->write("do not attack");
   $response->end();
   return;
}
```
或者是
```
$cookie = $request->getCookieParams('who');
//do cookie auth
if(auth fail){
   $response->end();
   return;
}
```
> 若在改事件中，执行 $response->end(),则该次请求不会进入路由匹配阶段。

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
