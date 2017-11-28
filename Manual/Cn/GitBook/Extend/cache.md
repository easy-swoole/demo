
es-cache
------

非常轻便的缓存实现，支持`File`和`Redis`缓存驱动

> 仓库地址: [Github](https://github.com/easy-swoole/cache)

安装
------

```
composer require easyswoole/cache
```

快速入门
------

如果不做任何设置，默认使用File驱动，开箱即用

```
use easySwoole/Cache/Cache;

// 设置缓存
Cache::set('CacheName', 'CacheValues');
// 获取缓存
Cache::get('CacheName');
// 删除缓存
Cache::delete('CacheName');
```

使用文件驱动
------

```
use easySwoole\Cache\Cache;
use easySwoole\Cache\Drivers\Files;

$FileDriver = new Files('cachePath');
Cache::init($FileDriver);

// 切换其他缓存目录
$FileDriver->setCachePath('otherCachePath');
Cache::init($FileDriver);

Cache::set('CacheName', 'CacheValues');
```

使用Redis驱动
------

```
use easySwoole\Cache\Cache;
use easySwoole\Cache\Drivers\Redis;

$RedisDriver = new Redis('127.0.0.1');

// 额外设置
$RedisDriver->setHost('127.0.0.1');
$RedisDriver->setPort(6379);
$RedisDriver->setPassword('AuthPass');
$RedisDriver->setPrefix('Cache:');
$RedisDriver->setDatabase(1);
$RedisDriver->setReconnect(false);

Cache::init($RedisDriver);
Cache::set('CacheName', 'CacheValues');
```

缓存操作函数原型
------

```
Cache::set(缓存名称, 缓存值, 超时时间);  // 设置
Cache::get(缓存名称);  // 读取
Cache::delete(缓存名称);  // 删除
Cache::has(缓存名称);  // 是否存在
Cache::clear();  // 清空

```