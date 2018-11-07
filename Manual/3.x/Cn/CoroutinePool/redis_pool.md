## Redis协程连接池
demo中有封装好的redis连接池以及redis类，地址: https://github.com/easy-swoole/demo/blob/3.x/App/Utility/Pool/RedisPool.php，复制demo中的RedisPool.php和RedisObject.php并放入App/Utility中即可使用

### 添加数据库配置
在env中添加配置信息：
```dotenv
################ REDIS CONFIG ##################

REDIS.host = 127.0.0.1
REDIS.port = 6379
REDIS.auth =
REDIS.POOL_MAX_NUM = 4
REDIS.POOL_TIME_OUT = 0.1
```
在EasySwooleEvent初始化事件initialize注册该连接池
```php
// 注册redis连接池

PoolManager::getInstance()->register(RedisPool::class, Config::getInstance()->getConf('REDIS.POOL_MAX_NUM'));
```

### 使用

通过redis连接池获取redis操作对象

```php
$redis = PoolManager::getInstance()->getPool(RedisPool::class)->getObj(Config::getInstance()->getConf('REDIS.POOL_TIME_OUT'));
$redis->set('name', 'blank');
$name = $redis->get('name');
var_dump($name);
/*
 * string(5) "blank"
 */
```
用完redis连接池对象之后记得用recycleObj回收

```php
PoolManager::getInstance()->getPool(RedisPool::class)->recycleObj($redis);
```