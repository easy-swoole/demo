## 请求方法结束后执行

###  函数原型
```php
public static function afterRequest(Request $request, Response $response): void
{
}
```

## 示例

使用trace组件,在onRequest中开启,再afterRequest中结束,可追踪一个请求进来的处理时间以及处理过程

```php


public static function onRequest(Request $request, Response $response): bool
{
    //为每个请求做标记
    TrackerManager::getInstance()->getTracker()->addAttribute('workerId', ServerManager::getInstance()->getSwooleServer()->worker_id);
    return true;
}

public static function afterRequest(Request $request, Response $response): void
{
    // TODO: Implement afterAction() method.
    TrackerManager::getInstance()->closeTracker();
}
```