# 缓存类

------

> Github : [ThinkORM](https://github.com/top-think/think-orm) - 从ThinkPHP5.1独立出来的缓存管理类库

安装
------

```bash
composer require topthink/think-cache
```

创建缓存类配置
------

修改 `Conf/Config.php` 文件，在userConf方法中添加如下配置，缓存类支持多种驱动，这里仅列出使用`File`驱动需要的配置项，具体不同的驱动使用的配置项，可以到类库目录的`driver`目录中打开对应的驱动文件，查看支持的完整配置

```php
private function userConf()
{
  return array(
    'cache' => [
      // 驱动方式（支持file/memcache/redis/xcache/wincache/sqlite）
      'type'   => 'File',
      // 缓存保存目录
      'path'   => ROOT . '/Temp/Cache/',
      // 缓存前缀
      'prefix' => '',
      // 缓存有效期 0表示永久缓存
      'expire' => 0,
    ]
  );
}
```

全局初始化缓存类
------

在`Conf/Event.php`的框架`初始化完成事件`中初始化数据库类配置，初始化完成后，即可在全局任意位置使用缓存类

```php
function frameInitialized()
{
    // 获得数据库配置
    $cacheConf = Config::getInstance()->getConf('cache');
    // 全局初始化
    Cache::init($cacheConf);
}
```

缓存操作示例
------

和`ThinkPHP`的使用方法一致，初始化完成后即可全局静态调用

```php
// 设置缓存
Cache::set('val','value',600);
// 判断缓存是否设置
Cache::has('val');
// 获取缓存
Cache::get('val');
// 删除缓存
Cache::rm('val');
// 清除缓存
Cache::clear();
// 读取并删除缓存
Cache::pull('val');
// 不存在则写入
Cache::remember('val','value');

// 对于数值类型的缓存数据可以使用
// 缓存增+1
Cache::inc('val');
// 缓存增+5
Cache::inc('val',5);
// 缓存减1
Cache::dec('val');
// 缓存减5
Cache::dec('val',5);

```

多种缓存驱动切换
------

可以在使用过程中动态的切换到另一种缓存实现，如下面的代码

```php
// 连接到另一缓存实现
$redis = Cache::connect([
    'type'   => 'redis',
    'host'   => '127.0.0.1',
    'port'   => 6379,
    'prefix' => '',
    'expire' => 0,
]);

// 进行缓存操作
$redis->set('var', 'value', 600);
$redis->get('var');

// 或者使用
$redis->val = 'value';
echo $redis->val;

```