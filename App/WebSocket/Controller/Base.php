<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:54
 */

namespace App\WebSocket\Controller;

use App\Utility\AppConst;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\RedisPoolObject;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

/**
 * 基础控制器
 * Class Base
 * @package App\WebSocket\Controller
 */
class Base extends Controller
{
    /** @var RedisPoolObject $redis */
    private $redis;

    /**
     * 获取Redis对象
     * @return RedisPoolObject|mixed|null
     * @throws \Exception
     */
    protected function redis()
    {
        if (!($this->redis instanceof RedisPoolObject)) {
            $redisPoolObject = PoolManager::getInstance()->getPool(RedisPool::class)->getObj(0.1);
            if ($redisPoolObject instanceof RedisPoolObject) {
                $this->redis = $redisPoolObject;
            } else {
                throw new \Exception('Get redis failed!');
            }
        }
        return $this->redis;
    }

    /**
     * 获取当前的用户
     * @return array|string
     * @throws \Exception
     */
    protected function currentUser()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        return $this->redis()->hGet(AppConst::REDIS_ONLINE_KEY, $client->getFd());
    }

    /**
     * 回收Redis对象
     * @return void
     */
    function __destruct()
    {
        if ($this->redis instanceof RedisPoolObject) {
            PoolManager::getInstance()->getPool(RedisPool::class)->recycleObj($this->redis);
        }
    }
}