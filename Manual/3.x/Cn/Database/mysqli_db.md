# MysqliDb

鉴于每个用户的使用习惯问题，EasySwoole本身并不提供封装好的数据库操作与Model层，但我们强力推荐在项目中使用第三方开源库<https://github.com/joshcam/PHP-MySQLi-Database-Class> 作为数据操作类库，并构建自己的Model。

## 使用指导

### 项目引入

方法一：

- 克隆(下载)MySQLi项目，并解压提取其中的MysqliDb.php文件，放入项目 '/App/Vendor/Db/' 路径下。

- Class引入。在composer中引入MysqliDb.php。

```json
{
  "autoload":{
    "psr-4":{
      "MysqliDb" : "App/Vendor/Db/MysqliDb.php"
    }
  }
}
```


方法二：

```bash
composer require joshcam/mysqli-database-class:dev-master
```

  > 引入成功后,用连接池的方式创建MysqliDb对象。

## 创建数据库配置

在env文件添加配置项。

```dotenv

MYSQL.host = 127.0.0.1
MYSQL.username = root
MYSQL.password = root
MYSQL.db = db
MYSQL.port = 3306

```

### 注入MYSQL连接池

在框架的\EasySwoole\EasySwoole\EasySwooleEvent::initialize，注入MYSQL连接池。

```php
PoolManager::getInstance()->register(MysqlPool::class);
```
### 数据库连接池类

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-9-25
 * Time: 下午4:37
 */

namespace App\Utility\Pools;


use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\EasySwoole\Config;

class MysqlPool extends AbstractPool
{

    protected function createObject()
    {
        // TODO: Implement createObject() method.
        $conf = Config::getInstance()->getConf('MYSQL');
        return new MysqlPoolObject($conf);
    }
}
```

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-9-25
 * Time: 下午4:37
 */

namespace App\Utility\Pools;


use EasySwoole\Component\Pool\PoolObjectInterface;

class MysqlPoolObject extends \MysqliDb implements PoolObjectInterface
{
    function __construct($config)
    {
        $host = $config['host'];
        $username = $config['username'];
        $password = $config['password'];
        $db = $config['db'];
        $port = $config['port'];
        $charset = $config['charset'] ?? 'utf-8';
        parent::__construct($host, $username, $password, $db, $port, $charset);
    }

    function gc()
    {
        $this->rollback();
        $this->disconnect();
    }

    /**
     * 重置mysql查询条件等信息
     */
    function objectRestore()
    {
        $this->rollback();
        $this->reset();
    }
}
```


获取数据库示例

```
$db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
```

### 数据库基础用法

#### Insert

```
$data = Array (
    "productName" => "test product",
    "userId" => $userIdQ,
    "lastUpdated" => $db->now()
);
$id = $db->insert ("products", $data);
// Gives INSERT INTO PRODUCTS (productName, userId, lastUpdated) values ("test product", (SELECT name FROM users WHERE id = 6), NOW());

```

#### Update Query

```php
$data = Array (
    'firstName' => 'Bobby',
    'lastName' => 'Tables',
    'editCount' => $db->inc(2),
    // editCount = editCount + 2;
    'active' => $db->not()
    // active = !active;
);
$db->where ('id', 1);
if ($db->update ('users', $data))
    echo $db->count . ' records were updated';
else
    echo 'update failed: ' . $db->getLastError();

```

update() also support limit parameter:

```php
$db->update ('users', $data, 10);
```

> // Gives: UPDATE users SET ... LIMIT 10

#### Select Query

After any select/get function calls amount or returned rows is stored in $count variable

```php
$users = $db->get('users'); //contains an Array of all users 
$users = $db->get('users', 10); //contains an Array 10 users

```

or select with custom columns set. Functions also could be used

```php
$cols = Array ("id", "name", "email");
$users = $db->get ("users", null, $cols);
if ($db->count > 0)
    foreach ($users as $user) { 
        print_r ($user);
    }

or select just one row

$db->where ("id", 1);
$user = $db->getOne ("users");
echo $user['id'];

$stats = $db->getOne ("users", "sum(id), count(*) as cnt");
echo "total ".$stats['cnt']. "users found";

or select one column value or function result

$count = $db->getValue ("users", "count(*)");
echo "{$count} users found";

```

select one column value or function result from multiple rows:

```php
$logins = $db->getValue ("users", "login", null);
// select login from users
$logins = $db->getValue ("users", "login", 5);
// select login from users limit 5
foreach ($logins as $login)
    echo $login;

```

#### JOIN method

```php
$db->join("users u", "p.tenantID=u.tenantID", "LEFT");
$db->where("u.id", 6);
$products = $db->get ("products p", null, "u.name, p.productName");
print_r ($products);

```

Join Conditions

Add AND condition to join statement

```php
$db->join("users u", "p.tenantID=u.tenantID", "LEFT");
$db->joinWhere("users u", "u.tenantID", 5);
$products = $db->get ("products p", null, "u.name, p.productName");
print_r ($products);
// Gives: SELECT  u.login, p.productName FROM products p LEFT JOIN users u ON (p.tenantID=u.tenantID AND u.tenantID = 5)

```

Add OR condition to join statement

```php
$db->join("users u", "p.tenantID=u.tenantID", "LEFT");
$db->joinOrWhere("users u", "u.tenantID", 5);
$products = $db->get ("products p", null, "u.name, p.productName");
print_r ($products);
// Gives: SELECT  u.login, p.productName FROM products p LEFT JOIN users u ON (p.tenantID=u.tenantID OR u.tenantID = 5)

```

> 更多用法请见: <https://github.com/joshcam/PHP-MySQLi-Database-Class>

### 创建Bean

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-17
 * Time: 上午9:45
 */

namespace App\Model\Goods;


use EasySwoole\Spl\SplBean;

class GoodBean extends SplBean
{
    protected $goodsId;
    protected $goodsName;
    protected $addTime;

    protected function initialize(): void
    {
        $this->addTime = time();
    }

    /**
     * @param mixed $goodsId
     */
    public function setGoodsId($goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param mixed $goodsName
     */
    public function setGoodsName($goodsName): void
    {
        $this->goodsName = $goodsName;
    }

    /**
     * @return mixed
     */
    public function getGoodsName()
    {
        return $this->goodsName;
    }
}

```

### 创建自己的Model

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-10
 * Time: 上午10:10
 */

namespace App\Model;


use App\Utility\Pools\MysqlPool;
use App\Utility\Pools\MysqlPoolObject;
use EasySwoole\Component\Pool\PoolManager;

class Base
{
    private $db;
    function __construct()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        if ($db instanceof MysqlPoolObject) {
            $this->db = $db;
        } else {
            throw new \Exception('Db pool is empty');
        }
    }

    protected function getDb() {
        return $this->db;
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->db instanceof MysqlPoolObject) {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);
        }
    }

}

```

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-17
 * Time: 上午9:44
 */

namespace App\Model\Goods;


use App\Model\Base;

class Goods extends Base
{

    protected $tableName = 'goods_list';

    function add(GoodBean $bean){
        return $this->getDb()->insert($this->tableName,$bean->toArray(null, $bean::FILTER_NOT_NULL));
    }
}

```

> 注意：数据库若使用单例模式保持长连接，一定要处理断线问题。本文中推荐的数据库类已经处理了。