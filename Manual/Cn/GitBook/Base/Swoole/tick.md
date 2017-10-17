# 定时器
通过调用swoole_server->tick()可以新增一个定时器。
> worker进程结束运行后，所有定时器都会自动销毁</br>
  tick/after定时器不能在swoole_server->start之前使用
  
## 在request事件中使用
```
   function onRequest()use($server) {
       $server->tick(1000, function() use ($server, $fd) {
           echo "hello world";
       });
   }
```
## 在onWorkerStart中使用
   
 - 低于1.8.0版本task进程不能使用tick/after定时器，所以需要使用$serv->taskworker进行判断
 - task进程可以使用addtimer间隔定时器
```
function onWorkerStart(swoole_server $serv, $worker_id)
{
    if (!$serv->taskworker) {
        $serv->tick(1000, function ($id) {
            var_dump($id);
        });
    }
    else
    {
        $serv->addtimer(1000);
    }
}
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
