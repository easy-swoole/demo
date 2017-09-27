# 封装单例模式的Redis持久链接
## 设置Redis链接信息
修改Config.php的User config，加入以下信息
```
"REDIS"=>array(
         "HOST"=>'ip',
         "PORT"=>port,
         "AUTH"=>'password'
        )
```
## Redis class
```
namespace App\Vendor\Db;


use Conf\Config;

class Redis
{
    private static $instance;
    private $con;

    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Redis();
        }
        return self::$instance;
    }

    function __construct()
    {
        $conf = Config::getInstance()->getConf("REDIS");
        $this->con = new \Redis();
        $this->con->connect($conf['HOST'],$conf['PORT']);
        $this->con->auth($conf['AUTH']);
        $this->con->setOption(\Redis::OPT_SERIALIZER,\Redis::SERIALIZER_PHP);
    }

    function getConnect(){
        return $this->con;
    }
}
```

> Redis 高版本的单机版已经在内部处理了断线问题。