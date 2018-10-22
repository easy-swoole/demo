# 主服务创建事件

## 函数原型
```php
@param \EasySwoole\EasySwoole\Swoole\EventRegister $register
public static function mainServerCreate(EventRegister $register)
{
}
```
## 已完成工作
在执行该事件的时候，已经完成的工作有：
- 框架初始化事件
- 配置文件加载完成
- 主Swoole Server创建成功
- 主Swoole Server 注册了默认的onRequest,onTask,onFinish事件。

## 可处理内容

### 注册主服务回调事件
例如为主服务注册onWorkerStart事件
```php
$register->add($register::onWorkerStart,function (\swoole_server $server,int $workerId){
     var_dump($workerId.'start');
});
```
### 添加一个自定义进程
```php
 ServerManager::getInstance()->getSwooleServer()->addProcess((new Test('test_process'))->getProcess());
```
> Test 是一个EasySwoole\Core\Swoole\Process\AbstractProcess子类

### 添加一个子服务监听
```php
$subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0',9503,SWOOLE_TCP);
$subPort->on('receive',function (\swoole_server $server, int $fd, int $reactor_id, string $data){
    var_dump($data);
});
```

> 使用参考：https://github.com/easy-swoole/demo/blob/3.x/EasySwooleEvent.php