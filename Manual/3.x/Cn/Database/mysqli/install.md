## 安装  
安装命令:
```
composer require easyswoole/mysqli
```

### 配置

```dotenv
MYSQL.host = 127.0.0.1   // 数据库地址
MYSQL.user = root        // 数据库用户名   
MYSQL.password = root    // 数据库密码
MYSQL.database = db      // 数据库库名
MYSQL.port = 3306        // 数据库端口
```
### 调用
```php
<?php
$conf = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
$db = new Mysqli($conf);
$data = $db->get('test');//获取一个表的数据
```

### 协程连接池
由于是协程状态,每次请求进来都必须使用不同的实例,如果一个请求进来就new,完成就销毁,每次都会调用php的gc机制,会大大地降低服务器的性能,所以我们可以采用连接池方式,复用对象,
[协程连接池教程](../../CoroutinePool/mysql_pool.md);
