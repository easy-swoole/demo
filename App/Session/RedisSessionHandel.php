<?php


namespace App\Session;


use EasySwoole\RedisPool\Redis;

class RedisSessionHandel implements \SessionHandlerInterface
{

    private $prefix = 'session_';//前缀
    private $redisPoolName = 'redis';//redispool名称
    private $ttl = 30 * 3600;//半小时过期

    public function __construct($prefix = 'session_', $redisPoolName = 'redis', $ttl = 30 * 3600)
    {
        $this->prefix = $prefix;
        $this->redisPoolName = $redisPoolName;
        $this->ttl = $ttl;
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        return Redis::invoke("", function (\EasySwoole\Redis\Redis $redis) use ($session_id) {
            $redis->del($this->prefix . $session_id);
        });
    }

    public function gc($maxlifetime)
    {
        return true;
    }

    public function open($save_path, $name)
    {
        return true;
    }

    public function read($session_id)
    {
        return Redis::invoke($this->redisPoolName, function (\EasySwoole\Redis\Redis $redis) use ($session_id) {
            $redis->expire($this->prefix . $session_id, $this->ttl);
            return $redis->get($this->prefix . $session_id);
        });
    }

    public function write($session_id, $session_data)
    {
        return Redis::invoke($this->redisPoolName, function (\EasySwoole\Redis\Redis $redis) use ($session_id, $session_data) {
           return $redis->set($this->prefix . $session_id, $session_data, $this->ttl);
        });
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string $redisPoolName
     */
    public function setRedisPoolName(string $redisPoolName): void
    {
        $this->redisPoolName = $redisPoolName;
    }

    /**
     * @param float|int $ttl
     */
    public function setTtl($ttl): void
    {
        $this->ttl = $ttl;
    }

}
