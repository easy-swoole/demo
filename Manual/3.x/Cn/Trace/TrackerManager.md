
## 方法列表  

###  setTokenGenerator()  
设置链追踪器的token生成方法,如果不设置该方法,则会抛出异常,示例:  
```php
  //调用链追踪器设置Token获取值为协程id
  TrackerManager::getInstance()->setTokenGenerator(function () {
      return \Swoole\Coroutine::getuid();
  });
```

### setEndTrackerHook()
设置链追踪器结束后的操作,示例:  
```php
  //每个链结束的时候，都会执行的回调
  TrackerManager::getInstance()->setEndTrackerHook(function ($token, Tracker $tracker) {
      //$token,追踪器token,$tracker追踪的数据
      Logger::getInstance()->console((string)$tracker);
  });
```
### getTracker($token=null)
通过token 获取一个链追踪器
```php
TrackerManager::getInstance()->getTracker(\Swoole\Coroutine::getuid());
```

### removeTracker($token=null)
移除一个链追踪器
```php
TrackerManager::getInstance()->removeTracker(\Swoole\Coroutine::getuid());
```
### closeTracker()
关闭一个链追踪器,并且调用setEndTrackerHook()设置的回调
```php
TrackerManager::getInstance()->closeTracker(\Swoole\Coroutine::getuid());
```
### clear()
清除一个链追踪器
```php
TrackerManager::getInstance()->removeTracker(\Swoole\Coroutine::getuid());
```
### getTrackerStack()
获取当前所有的链追踪器

```php
TrackerManager::getInstance()->getTrackerStack();
```
### token($token)
返回一个token,如果$token===null,则调用setTokenGenerator()设置的token生成器生成token


