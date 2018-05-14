## Redis协程连接池
demo中有封装好的redis连接池，[RedisPool.php](https://github.com/easy-swoole/demo/blob/master/Application/Utility/RedisPool.php)，复制demo中的RedisPool.php并放入App/Utility中即可使用

### 添加数据库配置
在Config中添加配置信息：
```php
'REDIS' => [
    'host' => '127.0.0.1', // redis主机地址
    'port' => 6379, // 端口
    'serialize' => false, // 是否序列化php变量
    'dbName' => 1, // db名
    'auth' => null, // 密码
    'pool' => [
        'min' => 5, // 最小连接数
        'max' => 100 // 最大连接数
    ],
    'errorHandler' => function(){
        return null;
    } // 如果Redis重连失败，会判断errorHandler是否callable，如果是，则会调用，否则会抛出异常，请自行try
]
```
并在Config的COROUTINE_POOL中新增该连接池
```php
'POOL_MANAGER' => [
    'App\Utility\RedisPool' => [
        'min' => 5,
        'max' => 100,
        'type' => 1
    ]
]
```

### 使用
获取到对象后，可以使用exec方法来执行任何命令，例如：
```php
$redis = $pool->getObj(); // 这里的pool是通过poolManager获取的RedisPool
$redis->exec('set', 'a', '123');
$a = $redis->exec('get', 'a');
$pool->freeObj($redis);
```

### 注意
尚未实现的方法```scan object sort migrate hscan sscan zscan```
其他说明详见[Mysql协程连接池](mysql_pool.md)
