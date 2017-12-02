迁移cache分页
---

> 仓库地址: [cache](https://github.com/illuminate/cache)

安装
------

```
composer require illuminate/cache
```

暂时实现 redis方式
-----
还需安装
```
composer require illuminate/redis
composer require predis/predis  //个人比较喜欢predis
```

启动predis
```
function frameInitialized()
{

    // redis
    \Predis\Autoloader::register();
}
```

修改`Conf/Config.php`在`userConf`方法中添加如下配置

```
private function userConf()
	{
		return array(
            "redis"=>array(
                    'cluster' => false,
                    'default' => array(
                        'host'     => '127.0.0.1',
                        'port'     => 6379,
                        'database' => 0,
                    ),
                    'redis' => array(
                        'host'     => '127.0.0.1',
                        'port'     => 6379,
                        'database' => 1,
                    ),
            ),
		);
	}
```

先获取cache单例
-----
```
namespace App\Vendor\DB;

use Conf\Config;
use Illuminate\Cache\RedisStore;

class Cache
{
    /**
     * @var void
     */
    private static $_instance = null;
    /**
     * @return Cache
     */
    static public function getInstance() {
        if (is_null ( self::$_instance ) || isset ( self::$_instance )) {
            self::$_instance = new self ();
        }
        return self::$_instance;
    }

    /**
     * @param string $connection 
     * @param string $driver  phpredis/predis 
     * @param string $prefix
     * @return \Predis\ClientInterface
     */
    public function redisConnect($connection = '', $driver = '', $prefix = ''){
        $config = Config::getInstance()->getConf('redis');
        $connection = is_null($connection) ? $connection : 'default';
        $driver = is_null($driver) ? $driver : 'predis';
        $prefix = is_null($prefix) ? $prefix : 'es';
        $redis =new \Illuminate\Redis\RedisManager($driver,$config);
        $cache = new RedisStore($redis,$prefix,$connection);
        return $cache;
    }
}
```



测试集成是否正常
------
让我们先确认一下cache是否能正常工作

```
use App\Vendor\Db\Cache;


// 在Index控制器类添加以下方法
function index()
{
    //可以使用predis的方法,需要connection一下
    $default =  Cache::getInstance()->redisConnect()->connection();
    $default->set('test', 'redis');
    $client = $default->get('test');
    $this->response()->write($client);

    //可以连接不同的redis库,也可以设置过期时间
    $redis = Cache::getInstance()->redisConnect('redis');
    $minute = 1;//以分钟
    echo $redis->tags('user')->remember('user:es', $minute, function(){
        return 'es';
    });
}
```
重启服务后访问`http://localhost:9501`看到redis,控制台打印es，就可以使用cache了