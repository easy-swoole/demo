
easySwoole Cache
------
[![StyleCI](https://styleci.io/repos/110963506/shield?style=flat&branch=master)](https://styleci.io/repos/110963506)
[![Latest Stable Version](https://poser.pugx.org/easyswoole/cache/version)](https://packagist.org/packages/easyswoole/cache)
[![Total Downloads](https://poser.pugx.org/easyswoole/cache/downloads)](https://packagist.org/packages/easyswoole/cache)
[![Latest Unstable Version](https://poser.pugx.org/easyswoole/cache/v/unstable)](//packagist.org/packages/easyswoole/cache)
[![License](https://poser.pugx.org/easyswoole/cache/license)](https://packagist.org/packages/easyswoole/cache)

一个轻量级的缓存实现，目前已支持 `Redis` `Memcache` `Memcached` `File` 四种储存模式

> 仓库地址: [Github](https://github.com/easy-swoole/cache)

安装
------

```
composer require easyswoole/cache
```

> 注意: 请确保框架已经引入了 `composer` 的 `autoload.php` 文件，否则报类不存在的错误

快速入门
------

如果不做任何设置，默认使用File驱动，开箱即用

```php
use easySwoole\Cache\Cache;

// 获取缓存
Cache::get('name', 'default');
// 设置缓存(10秒后过期)
Cache::set('name', 'value', 10);
// 判断缓存是否存在
Cache::has('name');
// 删除缓存值
Cache::delete('name');
// 清空所有缓存
Cache::clear();

// 对于数值类型的缓存可以使用自增自减方法

// 缓存值自增+1
Cache::inc('name');
// 缓存值自增+10
Cache::inc('name', 10);
// 缓存值自减-1
Cache::dec('name');
// 缓存值自减-10
Cache::dec('name', 10);

// 另外还有两个语法糖

// 获取并删除缓存(相当于pop操作)
Cache::pull('name', 'default');

// 不存在则插入 存在则返回值
Cache::remember('name', 'value', 10);
```

初始化缓存驱动
------

驱动初始化是非常简单的，只需要定义好配置数组，然后在`框架初始化完成`事件中进行初始化，可以直接复制以下代码进行修改!

### 文件系统驱动

- 缓存过期时间设置为0，则缓存永不过期，其他驱动也一样
- 如果没有设置缓存path，则自动获取PHP的临时目录创建缓存
- 数据压缩仅在支持gzcompress的状态下可用，否则打开了也不会压缩
- 线程安全模式是指操作读写之前会进行上锁 避免脏读

```php
use easySwoole\Cache\Cache;
use easySwoole\Cache\Connector\Files;

function frameInitialized()
{
    $fileSystemOptions = [
        'expire'        => 0,     // 缓存过期时间
        'cache_subdir'  => true,  // 开启子目录存放
        'prefix'        => '',    // 缓存文件后缀名
        'path'          => '',    // 缓存文件储存路径
        'hash_type'     => 'md5', // 文件名的哈希方式
        'data_compress' => false, // 启用缓存内容压缩
        'thread_safe'   => false, // 线程安全模式
        'lock_timeout'  => 3000,  // 文件最长锁定时间(ms)
    ];
    $FilesConnector = new Files($fileSystemOptions);
    Cache::init($FilesConnector);
}
```

### Redis驱动

- 需要PHP扩展`redis`支持，PECL库地址 : [http://pecl.php.net/package/redis](http://pecl.php.net/package/redis)
- 设置了全局的`expire`，在Set操作的时候没有指定过期时间，所有key都会以全局为准

```php
use easySwoole\Cache\Cache;
use easySwoole\Cache\Connector\Redis;

function frameInitialized()
{
    $redisOptions = [
        'host'       => '127.0.0.1',  // Redis服务器
        'port'       => 6379,         // Redis端口
        'password'   => '',           // Redis密码
        'select'     => 0,            // Redis库序号
        'timeout'    => 0,            // 连接超时
        'expire'     => 0,            // 默认缓存超时
        'persistent' => false,        // 是否使用长连接
        'prefix'     => 'cache:',     // 缓存前缀
    ];
    $redisConnector = new Redis($redisOptions);
    Cache::init($redisConnector);
}
```

### Memcache驱动

- 支持集群连接，集群模式下的`host`和`port`用逗号分开，上下对应即可

```php
use easySwoole\Cache\Cache;
use easySwoole\Cache\Connector\Memcache;

function frameInitialized()
{
    $memcacheOptions = [
        'host'       => '127.0.0.1',  // Memcache服务器
        'port'       => 11211,        // Memcache端口
        'expire'     => 0,            // 默认缓存过期时间
        'timeout'    => 0,            // 连接超时
        'persistent' => true,         // 是否启用长连接
        'prefix'     => '',           // 缓存前缀
    ];
    $memcacheConnector = new Memcache($memcacheOptions);
    Cache::init($memcacheConnector);
}
```

### Memcached驱动

- 同样支持集群连接，如果memcache编译的时候启用了SASL支持，则支持账号密码认证

```php
use easySwoole\Cache\Cache;
use easySwoole\Cache\Connector\Memcached;

function frameInitialized()
{
    $memcachedOptions = [
        'host'     => '127.0.0.1',  // Memcache服务器
        'port'     => 11211,        // Memcache端口
        'expire'   => 0,            // 默认缓存过期时间
        'timeout'  => 0,            // 超时时间（单位：毫秒）
        'prefix'   => '',           // 缓存后缀
        'username' => '',           // Memcache账号
        'password' => '',           // Memcache密码
        'option'   => [],           // Memcache连接配置
    ];
    $memcachedConnector = new Memcached($memcacheOptions);
    Cache::init($memcachedConnector);
}
```

### 传入配置直接实例化

可以将配置保存到 `Conf\Config.php` 文件，然后载入配置进行初始化，从`1.1.0`版本开始支持在配置中指定`driver`选项，自动识别驱动进行配置，以文件驱动为例，参照如下代码进行配置

```php
private function userConf()
{
    return array(
        'cache' => [
            'driver'        => 'files', // 驱动名称
            'expire'        => 0,       // 缓存过期时间
            'cache_subdir'  => true,    // 开启子目录存放
            'prefix'        => '',      // 缓存文件后缀名
            'path'          => '',      // 缓存文件储存路径
            'hash_type'     => 'md5',   // 文件名的哈希方式
            'data_compress' => false,   // 启用缓存内容压缩
            'thread_safe'   => false,   // 线程安全模式
            'lock_timeout'  => 3000,    // 文件最长锁定时间(ms)
        ]
    );
}
```

在`框架初始化完成`事件中读取配置文件进行初始化

> 注意: 如果使用 Redis Memcache 等驱动 请勿在 onWorkerStart 事件之前进行除了初始化以外的操作 否则造成进程共用相同的连接

```php
use easySwoole\Cache\Cache;

function frameInitialized()
{
    $CacheOptions = Config::getInstance()->getConf('cache');
    Cache::init($CacheOptions);
}
```