# trace组件  
demo地址 https://github.com/easy-swoole/demo/tree/3.x  

es3.x提供了trace代码追踪组件,可在任意位置调用该组件,追踪打印数据,示例:   

>为了使用方便,我们需要增加TrackerManager.php文件,使其支持单例调用(注意命名空间):
```php
<?php 
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午12:02
 */

namespace App\Utility;

use EasySwoole\Component\Singleton;

class TrackerManager extends \EasySwoole\Trace\TrackerManager
{
    use Singleton;
}
```
在use Singleton之后,就可以使用单例模式在框架全局调用同一个Tracker,共用对象实例了,下面所有文档都使用了单例TrackerManager


```php
//调用链追踪器设置Token获取值为协程id
<?php
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
```
//设置追踪1
```php
<?php
$tracker = TrackerManager::getInstance()->getTracker();

$trackerPoint = $tracker->setPoint('查询用户余额',[
'sql'=>'sql statement one'
]);
//模拟sql one执行
//$mode->func();
usleep(3000);
$tracker->endPoint('查询用户余额',$trackerPoint::STATUS_SUCCESS,["调用成功"]);
$this->response()->write('call trace');//$this->response()在控制器中调用

```

//设置追踪2，模拟失败任务
```php
<?php

$tracker = TrackerManager::getInstance()->getTracker();

$trackerPoint = $tracker->setPoint('查询用户订单',[
'sql'=>'sql statement one'
]);
//模拟sql 执行
usleep(1000000);
$tracker->endPoint('查询用户订单',$trackerPoint::STATUS_FAIL,["查询失败"]);
$this->response()->write('call trace');
//关闭链追踪器
$tracker->closeTracker();

```

###可以使用Logger类进行打印,保存追踪日志,示例:

```php
<?php
$tracker = TrackerManager::getInstance()->getTracker();
Logger::getInstance()->console((string)$tracker);//输出到控制台并且保存到日志
Logger::getInstance()->log((string)$tracker);//直接保存到日志
```
### 记录日志为如下格式:
```

18-10-29 09:30:50:TrackerToken:2
Attribute:
	workerId:4
	user:用户名1
	name:这是昵称
Stack:
	#:
	pointName:查询用户订单
	pointCategory:default
	pointStatus:FAIL
	pointStartTime:1540776649.2616
	pointTakeTime:1.0008
	pointFile:/www/wwwroot/es3/Application/HttpController/Trace.php
	pointLine:28
	pointStartArgs:{"sql":"sql statement one"}
	pointEndArgs:["查询失败"]
```