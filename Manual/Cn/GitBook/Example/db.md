# Model与数据库
鉴于每个用户的使用习惯问题，EasySwoole本身并不提供封装好的数据库操作与Model层，但我们强力推荐在项目中使用第三方开源库[https://github.com/joshcam/PHP-MySQLi-Database-Class](https://github.com/joshcam/PHP-MySQLi-Database-Class)
作为数据操作类库，并构建自己的Model。
## 使用指导
### 项目引入
- 克隆(下载)MySQLi项目，并解压提取其中的MysqliDb.php文件，放入项目 '/App/Vendor/Db/' 路径下。
- Class引入。在框架的frameInitialize事件中引入MysqliDb.php。
    ```
    function frameInitialize()
    {
        // TODO: Implement frameInitialize() method.
        AutoLoader::getInstance()->requireFile("App/Vendor/Db/MysqliDb.php");
    }
    ```
    > 引入成功后即可在项目任意位置创建MysqliDb对象。
    
    
### 利用IOC容器实现单例长连接
在框架的frameInitialize中，引入了MysqliDb.php后，即可进行IOC注入。
```
Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
            'host' => 'host',
            'username' => 'username',
            'password' => 'password',
            'db'=> 'dbName',
            'port' => 3306,
            'charset' => 'utf8')
);
//或者是
Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
            'host',
            'username',
            'password',
            'dbName',
            3306,
            'utf8')
);

$db = Di::getInstance()->get('MYSQL');
```
> 注意：为避免出现多个进程复用同一个数据库连接的情况，请勿在服务启动前的任一位置执行Di::getInstance()->get('MYSQL')。
若在frameInitialize或者是beforeWorkerStart事件中使用数据库，请以手动new class()的方式来获取一个数据库对象。其次，在单例子模式下，请注意数据库断线重连问题。
MysqliDb类库中有实现断线自动重连。

### 创建自己的Model
```
namespace App\Model\Goods;


use Core\Component\Spl\SplBean;

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
```

```
namespace App\Model\Goods;


use Core\Component\Di;

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

>注意：数据库若使用单例模式保持长连接，一定要处理断线问题。本文中推荐的数据库类已经处理了。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
