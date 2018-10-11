# trace组件  
es3.x提供了trace代码追踪组件,可在任意位置调用该组件,追踪打印数据,示例:   

```php
//调用链追踪器设置Token获取值为协程id
TrackerManager::getInstance()->setTokenGenerator(function () {
    return \Swoole\Coroutine::getuid();
});
//每个链结束的时候，都会执行的回调
TrackerManager::getInstance()->setEndTrackerHook(function ($token, Tracker $tracker) {
//            var_dump((string)$token);
    Logger::getInstance()->console((string)$tracker);
});

//加入参数到链追踪器
TrackerManager::getInstance()->getTracker()->addAttribute('user','用户名1');
TrackerManager::getInstance()->getTracker()->addAttribute('name','这是昵称');

//设置追踪1
$caller = TrackerManager::getInstance()->getTracker()->addCaller('CurlBaiDu','wd=easyswoole');
file_get_contents('https://www.baidu.com/s?wd=easyswoole');
$caller->endCall();

//设置追踪2，模拟失败任务
$caller = TrackerManager::getInstance()->getTracker()->addCaller('CurlBaiDu2','wd=easyswoole');
file_get_contents('https://www.baidu.com/s?wd=easyswoole');
$caller->endCall($caller::STATUS_FAIL,'curl失败了');

//关闭链追踪器
TrackerManager::getInstance()->closeTracker();

```

###可以使用Logger类进行打印,保存追踪日志,示例:

```php
Logger::getInstance()->console((string)$tracker);//输出到控制台并且保存到日志
Logger::getInstance()->log((string)$tracker);//直接保存到日志
```
