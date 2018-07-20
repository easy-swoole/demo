# 自定义ReidsHandler实现用Redis管理Session

> 实现RedisHanler的重点是实现 SessionHandlerInterface,重写接口方法即可。熟悉代码还可以在此基础上扩展。

## 代码实现如下

```
namespace App\Utility;


use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;
use Redis;

class RedisSession implements \SessionHandlerInterface
{
    private $options = [
        'handler' => null,
        'host' => null,
        'port' => null,
        'prefix' =>'',
    ];

    /**
     * @return bool
     */
    public function close()
    {
        return $this->options['handler']->close();
        // TODO: Implement close() method.
    }

    /**
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id): bool
    {
        $session_id = $this->options['prefix'] . $session_id;
        return $this->options['handler']->delete($session_id) >= 1 ? true : false;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime): bool
    {
        // TODO: Implement gc() method.
        return true;
    }

    /**
     * @param $save_path
     * @param $name
     * @return bool
     */
    public function open($save_path, $name): bool
    {
        //读取redis配置文件，可以根据需要修改redis配置连接
        $conf = Config::getInstance()->getConf('redis');
        $this->options['host'] = $conf['host'];
        $this->options['port'] = $conf['port'];
        $this->options['prefix'] = $name.'_';
        $set = Di::getInstance()->get(SysConst::HTTP_SESSION_GC_MAX_LIFE_TIME);
        if (!empty($set)) {
            $maxLifeTime = $set;
        } else {
            $maxLifeTime = 3600 * 24 * 30;
        }
        $this->options['lifeTime'] = $maxLifeTime;
        if (is_resource($this->options['handler'])) return true;
        //连接redis
        $redisHandle = new Redis();
        $redisHandle->connect($this->options['host'], $this->options['port']);
        if (!$redisHandle) {
            return false;
        }
        $this->options['handler'] = $redisHandle;
        return true;
    }

    /**
     * @param string $session_id
     * @return string
     */
    public function read($session_id)
    {
        $session_id = $this->options['prefix'] . $session_id;
        return $this->options['handler']->get($session_id);
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        $session_id = $this->options['prefix'] . $session_id;
        return $this->options['handler']->setex($session_id, $this->options['lifeTime'], $session_data);
    }

}
```
> 然后在根目录的EasySwooleEevent.php文件 mainServerCreate 方法中注入该Handler，即可在代码中调用Session时，自动使用redis来存储seesion

```
    //注入Session Redis 处理Handler
    Di::getInstance()->set(SysConst::HTTP_SESSION_HANDLER,RedisSession::class);
    //注入Session Redis 过期时间
    Di::getInstance()->set(SysConst::HTTP_SESSION_GC_MAX_LIFE_TIME,3600);
```
