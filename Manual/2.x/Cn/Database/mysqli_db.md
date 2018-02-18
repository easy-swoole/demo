# MysqliDb

鉴于每个用户的使用习惯问题，EasySwoole本身并不提供封装好的数据库操作与Model层，但我们强力推荐在项目中使用第三方开源库<https://github.com/joshcam/PHP-MySQLi-Database-Class> 作为数据操作类库，并构建自己的Model。

## 使用指导

### 项目引入

方法一：

- 克隆(下载)MySQLi项目，并解压提取其中的MysqliDb.php文件，放入项目 '/App/Vendor/Db/' 路径下。

- Class引入。在composer中引入MysqliDb.php。

  ```Json
  "autoload": {
      "psr-4": {
          "MysqliDb" : "App/Vendor/Db/MysqliDb.php"
      }
  }
  ```


方法二：

```json
composer require joshcam/mysqli-database-class:dev-master
```

  > 引入成功后即可在项目任意位置创建MysqliDb对象。

### 利用IOC容器实现单例长连接

在框架的\EasySwoole\EasySwooleEvent::mainServerCreate中，引入了MysqliDb.php后，即可进行IOC注入。

```Php
Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
            'host' => 'host',
            'username' => 'username',
            'password' => 'password',
            'db'=> 'dbName',
            'port' => 3306,
            'charset' => 'utf8')
);
```

获取数据库示例

```
$db = Di::getInstance()->get('MYSQL');
```

> 注意：为避免出现多个进程复用同一个数据库连接的情况，请勿在服务启动前的任一位置执行Di::getInstance()->get('MYSQL')。 workerStart事件中使用数据库，请以手动new class()的方式来获取一个数据库对象，其次，在单例子模式下，请注意数据库断线重连问题， MysqliDb类库中有实现断线自动重连。

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

### 创建自己的Model

```php
namespace App\Model\Goods;
use EasySwoole\Core\Component\Spl\SplBean;
class Bean extends SplBean
{
    protected $goodsId;
    protected $goodsName;
    protected $addTime;
    protected function initialize()
    {
        // TODO: Implement initialize() method.
        $this->addTime = time();
    }

}

namespace App\Model\Goods;


use EasySwoole\Core\Component\Di;

class Goods
{
    protected $db;
    protected $tableName = 'goods_list';
    function __construct()
    {
        $db = Di::getInstance()->get("MYSQL");
        if($db instanceof \MysqliDb){
            $this->db = $db;
        }
    }

    function add(Bean $bean){
        return $this->db->insert($this->tableName,$bean->toArray($bean::FILTER_TYPE_NOT_NULL));
    }
}

```

> 注意：数据库若使用单例模式保持长连接，一定要处理断线问题。本文中推荐的数据库类已经处理了。