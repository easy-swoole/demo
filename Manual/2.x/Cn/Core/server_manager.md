## 服务管理者

在EasySwoole框架初始化后会执行ServerManager::start()来创建主服务、开启缓存、创建要监听的端口列表。

#### 命名空间地址

EasySwoole\Core\Swoole\ServerManager

### 方法列表

获得ServerManager单例：

```Php
public static function getInstance():ServerManager
```

添加服务：

- string `serverName` 服务名称
- int `port` 端口
- int `type` swoole支持的Socket类型，具体见：https://wiki.swoole.com/wiki/page/16.html
- string `host` host
- array `setting` 配置信息

```Php
public function addServer(string $serverName,int $port,int $type = SWOOLE_TCP,string $host = '0.0.0.0',array $setting = [
    "open_eof_check"=>false,
]):EventRegister
```

是否启动：

```php
public function isStart():bool
```

开启管理者：

本方法会创建主服务、开启缓存、创建要监听的端口列表

```php
public function start():void
```

获得进程服务：

- string `serverName` 当serverName为null 时返回主进程服务

```php
public function getServer($serverName = null):?\swoole_server
```

获得当前协程的唯一ID：

- 成功时返回当前协程`ID（int）`
- 如果当前不在协程环境中，则返回`-1`

```php
public function coroutineId():?int
```

当前是否为协程：

```php
public function isCoroutine():bool
```