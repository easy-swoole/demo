# 定时器
EasySwoole对定Swoole时器进行了封装。
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
Timer::delay(10*1000,function (){
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
## 实例
```
function onWorkerStart(\swoole_server $server, $workerId)
{
    // TODO: Implement onWorkerStart() method.
    //如何避免定时器因为进程重启而丢失
    //例如，我第一个进程，添加一个10秒的定时器
    if($workerId == 0){
        //每十秒执行一次
        Timer::loop(10*1000,function (){
            time();
            //从数据库，或者是redis中，去获取下个就近10秒内需要执行的任务
            //例如:2秒后一个任务，3秒后一个任务
            //那么
            Timer::delay(2*1000,function (){
                //为了防止因为任务阻塞，引起定时器不准确，吧任务给异步进程处理
                Logger::getInstance()->console("time 2",false);
            });
            Timer::delay(3*1000,function (){
                //为了防止因为任务阻塞，引起定时器不准确，吧任务给异步进程处理
                Logger::getInstance()->console("time 3",false);
            });
        });
    }
}
```

### 经典案例-订单状态超时监控
场景说明：在很多抢购的场景中，订单下单完成后，需要限制其付款时间，或者是在棋牌游戏中，需要对房间状态进行监控。那么我们
可以先把待监控的订单或者是房间压入redis队列中。那么利用定时器+异步进程，去实现对订单状态的循环监控。



