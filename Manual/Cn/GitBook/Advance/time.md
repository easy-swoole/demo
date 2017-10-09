# 定时器
Core\Swoole\Timer
## loop
```
//10秒执行一次
Timer::loop(10*1000,function (){
     Logger::getInstance()->console("this is timer");
});
```
## delay
```
//10秒后执行一次
Timer::loop(10*1000,function (){
     Logger::getInstance()->console("this is timer");
});
```
> 注意：定时器不能在服务启动之前使用。在服务启动以后，添加的定时器仅仅在当前进程中有效。在workerStart事件中添加定时器时，请注意判断需要添加定时器的workerId,否在该定时器在每个进程中均会被执行。


```
//为第一个worker添加一个定时器
if($workerId == 0){
   //10秒
   Timer::loop(10*1000,function (){
        Logger::getInstance()->console("this is timer");
   });
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