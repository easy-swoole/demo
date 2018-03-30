## 请求方法结束后执行

假如你使用了单例模式，需要清理请求时的GET POST 等全局变量或本次请求的日志运行记录，就可以在此方法内执行。

```Php
protected function afterAction( $actionName ) : void
```

## 示例

想一下，我如果想知道那些请求的执行时间长短，或者记录一些所谓的 慢请求， 那么我们可以通过两个事件 `onRequest` 和当前这个 `afterAction` 来完成。

看下代码，首先在 `onRequest` 增加一个时间戳，注意这是在请求开始执行之前触发的，所以我们可以精准的记录下来请求花费的时间。

```php
 $request->withAttribute('requestTime', microtime(true));
```

很简单，我只是给请求开始的时候给请求增加了一个属性 `requestTime` 用来记录开始时间。接下来是 `afterAction` 。

```php
//从请求里获取之前增加的时间戳
$reqTime = $request->getAttribute('requestTime');
//计算一下运行时间
$runTime = round(microtime(true) - $reqTime, 3);
//获取用户IP地址
$ip = ServerManager::getInstance()->getServer()->connection_info($request->getSwooleRequest()->fd);

//拼接一个简单的日志
$logStr = ' | '.$ip['remote_ip'] .' | '. $runTime . '|' . $request->getUri() .' | '.
    $request->getHeader('user-agent')[0];
    //判断一下当执行时间大于1秒记录到 slowlog 文件中，否则记录到 access 文件
if($runTime > 1){
    Logger::getInstance()->log($logStr, 'slowlog');
}else{
    logger::getInstance()->log($logStr,'access');
}
```

仅此而已
