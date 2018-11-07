## Mysql协程连接池
demo中有封装好的mysql连接池以及mysql类，地址: <https://github.com/easy-swoole/demo/blob/3.x/App/Utility/Pool/>，复制demo中的MysqlPool.php和MysqlObject.php并放入App/Utility中即可使用

### 添加数据库配置
在env中添加配置信息：
```dotenv
################ DATABASE CONFIG ##################

MYSQL.host = 127.0.0.1          // 数据库地址
MYSQL.port = 3306               // 数据库端口
MYSQL.user = root               // 数据库用户名   
MYSQL.timeout = 5
MYSQL.charset = utf8mb4         
MYSQL.password = root           // 数据库密码
MYSQL.database = easyswoole     // 数据库库名
MYSQL.POOL_MAX_NUM = 4
MYSQL.POOL_TIME_OUT = 0.1
```
在EasySwooleEvent初始化事件initialize注册该连接池
```php
// 注册mysql数据库连接池

PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));
```

### 注意
连接池不是跨进程的，进程间的连接池连接数是相互独立的，默认最大值是10个；如果开了4个worker，最大连接数可以达到40个。

### 使用

通过mysql连接池获取mysql操作对象

```php
$db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj(Config::getInstance()->getConf('MYSQL.POOL_TIME_OUT'));
```

用完mysql连接池对象之后记得用recycleObj回收

```php
PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
```
