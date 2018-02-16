# 主服务创建事件

## 函数原型
```php
@param \EasySwoole\Core\Swoole\ServerManager $server
@param \EasySwoole\Core\Swoole\EventRegister $register
public function mainServerCreate(ServerManager $server,EventRegister $register): void
{
}
```
## 已完成工作
在执行该事件的时候，已经完成的工作有：
- 框架初始化事件
- 主Swoole Server创建成功
- 主Swoole Server 注册了默认的onTask和onFinish事件。

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
ProcessManager::getInstance()->addProcess('test_process',Test::class);
```
> Test 是一个EasySwoole\Core\Swoole\Process\AbstractProcess子类

### 添加一个子服务
```php
$tcp = $server->addServer('tcp',9502);
   $tcp->set($tcp::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data){
   var_dump('rec'.$data);
});
```