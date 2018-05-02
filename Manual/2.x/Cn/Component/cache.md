## 缓存

#### 命名空间地址

EasySwoole\Core\Component\Cache\Cache

### 方法列表

获得键值：

- string `key` 缓存的键
- float `timeOut` 调度等待时间，默认等待0.01秒

```php
public function get($key,$timeOut = 0.01)
```

设置键值：

- string `key` 缓存的键
- mixed `data` 缓存的数据

```php
public function set($key,$data)
```

删除缓存：

- string `key` 缓存的键

```php
function del($key)
```

清空缓存：

```php
function flush()
```





```php
public function deQueue($key,$timeOut = 0.01)
```

```php
public function enQueue($key,$data)
```

```php
public function clearQueue($key)
```