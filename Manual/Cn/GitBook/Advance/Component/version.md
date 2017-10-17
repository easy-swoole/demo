# 版本控制
Easyswoole 提供了高自由度的版本控制插件，版本控制的代码实现在Core\Component\Version中;而版本控制的核心关键点在于对onRequest事件进行全局拦截，再做版本鉴定和请求重新分发。

## 使用
在frameInitialize事件中创建好版本控制实例和设置对应的规则。
```
function frameInitialize(){
   // TODO: Implement frameInitialize() method.
   date_default_timezone_set('Asia/Shanghai');
   $versionControl = new Control();
   $versionControl->addVersion("v1",function (Request $request){
   if($request->getRequestParam("version") == 1){
          return true;
        }
   })->addPathMap("/test",function (Request $request,Response $response){
            $response->writeJson(200,"v1");
   });
   $versionControl->addVersion("v2",function (Request $request){
       if($request->getRequestParam("version") == 2){
           return true;
       }
   })->addPathMap("/test",function (Request $request,Response $response){
            $response->writeJson(200,"v2");
   })->addPathMap("/test2","/");
   //注入容器
   Di::getInstance()->set(SysConst::VERSION_CONTROL,$versionControl);
}
```

在设置版本完版本控制规则后，在OnRequest事件中开启版本处理即可。
```
function onRequest(Request $request, Response $response)
{
    // TODO: Implement onRequest() method.
    Di::getInstance()->get(SysConst::VERSION_CONTROL)->startControl();
}
```
> 版本控制会先找到当前匹配version设置的回调结果进行处理，如果既不是路径字符串，也不是闭包，再找 control 实例的defaulthandler，也没有设置默认的再找 control 实例的defaulthandler，最后走dispatch直接解析 url 。

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
