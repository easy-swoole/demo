## Mysql协程连接池
demo中有封装好的mysql连接池，[MysqlPool.php](https://github.com/easy-swoole/demo/blob/3.x/Application/Utility/Pool/MysqlPool.php)，复制demo中的MysqlPool.php并放入App/Utility中即可使用

### 添加数据库配置
在env中添加配置信息：
```dotenv
MYSQL.host = 127.0.0.1   // 数据库地址
MYSQL.username = root    // 数据库用户名   
MYSQL.password = root    // 数据库密码
MYSQL.db = db            // 数据库库名
MYSQL.port = 3306        // 数据库端口
```
在EasySwooleEvent注册该连接池
```php
PoolManager::getInstance()->register(MysqlPool::class);
```

### 注意
连接池不是跨进程的，进程间的连接池连接数是相互独立的，默认最大值是10个；如果开了4个worker，最大连接数可以达到40个。

### 使用

```php
$pool = PoolManager::getInstance()->getPool(MysqlPool::class); // 获取连接池对象
$db = $pool->getObj();
```
获得Db对象。

### 连接池基本方法

#### getObj 从连接池中取得对象
```php
public function getObj($timeOut = 0.1) {}
```
timeOut指定超时时间（单位：秒），当连接池中没有对象时，将会进行等待，如果超时时间大于0，则最多等待该时间。

#### freeObj 释放对象
```php
public function recycleObj($obj) {}
```
将对象释放，重新放回连接池中。

### 注意
使用完后，一定要记得recycleObj。
