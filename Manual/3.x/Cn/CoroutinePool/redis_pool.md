## Redis协程连接池
demo中有封装好的redis连接池，[RedisPool.php](https://github.com/HeKunTong/easyswoole3_demo/blob/master/App/Utility/Pools/RedisPool.php)，复制demo中的RedisPool.php并放入App/Utility中即可使用

### 添加数据库配置
在env中添加配置信息：
```dotenv
REDIS.host = 127.0.0.1
REDIS.port = 6379
REDIS.password =
REDIS.select = 0
REDIS.timeout = 0
REDIS.expire = 0
REDIS.persistent = false
REDIS.prefix =
```
在EasySwooleEvent注册该连接池
```php
PoolManager::getInstance()->register(RedisPool::class);
```

### 使用
获取到对象后，可以使用exec方法来执行任何命令，例如：
```php
$pool = PoolManager::getInstance()->getPool(RedisPool::class);  // 获取连接池
$redis = $pool->getObj();                                       // 获取redis对象
$redis->exec('set', 'a', '123');
$a = $redis->exec('get', 'a');
$pool->recycleObj($redis);                                      // 回收连接池对象
```