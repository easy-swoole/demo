## Mysql协程连接池
demo中有封装好的mysql连接池，[MysqlPool2.php](https://github.com/easy-swoole/demo/blob/master/Application/Utility/MysqlPool2.php)，复制demo中的MysqlPool2.php并放入Application/Utility中即可使用

### 添加数据库配置
在Config中添加配置信息：
```php
'MYSQL' => [
    'HOST' => '127.0.0.1', // 数据库地址
    'PORT' => 3306, // 数据库端口
    'USER' => 'root', // 数据库用户名
    'PASSWORD' => 'root', // 数据库密码
    'DB_NAME' => 'db', // 数据库库名
    'MIN' => 5, // 最小连接数
    'MAX' => 100 // 最大连接数
]
```
并在Config的COROUTINE_POOL中新增该连接池
```php
COROUTINE_POOL => [
    [
        'class' => 'App\Utility\MysqlPool2',
        'min' => 5,
        'max' => 100,
        'type' => 1
    ]
]
```

### 注意
连接池不是跨进程的，也就是说一个进程有一个连接池，配置中的MAX为100，开了4个worker，最大连接数可能达到400。

### 使用
需要先```use EasySwoole\Core\Swoole\Coroutine\PoolManager;```，可以通过
```php
$pool = PoolManager::getInstance()->getPool('App\Utility\MysqlPool'); // 获取连接池对象
$db = $pool->getObj();
```
获得对象。协程的ORM是从[MysqliDb](/Manual/2.x/Cn/_book/Database/mysqli_db.html)移植的，操作与[MysqliDb](/Manual/2.x/Cn/_book/Database/mysqli_db.html)一致。

### 连接池基本方法

#### getObj 从连接池中取得对象
```php
public function getObj($timeOut = 0.1) {}
```
timeOut指定超时时间（单位：秒），当连接池中没有对象时，将会进行等待，如果超时时间大于0，则最多等待该时间。

#### freeObj 释放对象
```php
public function freeObj($obj) {}
```
将对象释放，重新放回连接池中。

### 注意
使用完后，一定要记得freeObj。
